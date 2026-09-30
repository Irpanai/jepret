<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\Photo;
use App\Models\Subscription;
use App\Models\SubscriptionOrder;
use App\Models\User;
use App\SubscriptionBilling;
use Database\Seeders\PackageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_photographer_without_subscription_is_redirected_to_plans(): void
    {
        $photographer = User::factory()->create([
            'role' => 'fotografer',
            'photographer_onboarded_at' => now(),
        ]);

        $this->actingAs($photographer)->get(route('fotografer.dashboard'))
            ->assertRedirect(route('subscriptions.plans', absolute: false));
        $this->actingAs($photographer)->get(route('fotografer.photos.create'))
            ->assertRedirect(route('subscriptions.plans', absolute: false));
    }

    public function test_photographer_with_pending_payment_is_redirected_to_payment_without_subscription(): void
    {
        $this->seed(PackageSeeder::class);
        $photographer = User::factory()->create([
            'role' => 'fotografer',
            'photographer_onboarded_at' => now(),
        ]);
        $order = app(SubscriptionBilling::class)->createOrder($photographer, Package::where('code', 'starter')->firstOrFail());
        $order->update(['qr_content' => 'pending-qris-content']);

        $this->actingAs($photographer)->get(route('fotografer.dashboard'))
            ->assertRedirect(route('subscriptions.payment', $order, absolute: false));

        $this->assertDatabaseMissing('subscriptions', ['user_id' => $photographer->id]);
        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_subscription_plans_show_active_database_packages_and_trial_action(): void
    {
        $this->seed(PackageSeeder::class);
        $photographer = User::factory()->create(['role' => 'fotografer']);

        $this->actingAs($photographer)->get(route('subscriptions.plans'))
            ->assertOk()
            ->assertSee('Aktifkan Trial')
            ->assertSee('Starter')
            ->assertSee('Creator')
            ->assertDontSee('Basic');
    }

    public function test_photographer_trial_can_be_activated_once_without_changing_roles(): void
    {
        $this->seed(PackageSeeder::class);
        $buyer = User::factory()->create([
            'role' => 'fotografer',
            'photographer_onboarded_at' => now(),
        ]);
        $trial = Package::where('code', 'trial')->firstOrFail();

        $this->actingAs($buyer)->post(route('subscriptions.checkout', $trial))->assertRedirect(route('fotografer.dashboard'));

        $buyer->refresh()->load('subscription');
        $this->assertTrue($buyer->hasRole('pembeli'));
        $this->assertTrue($buyer->hasRole('fotografer'));
        $this->assertTrue($buyer->subscription->isActive());
        $this->assertNotNull($buyer->subscription->trial_used_at);

        $this->actingAs($buyer)->post(route('subscriptions.checkout', $trial))->assertSessionHasErrors('trial');
        $this->assertSame(1, SubscriptionOrder::where('intent', 'trial')->count());
    }

    public function test_valid_webhook_activates_subscription_once_and_duplicate_is_idempotent(): void
    {
        $this->configureDoku();
        $this->seed(PackageSeeder::class);
        $buyer = User::factory()->pembeli()->create();
        $starter = Package::where('code', 'starter')->firstOrFail();
        $order = app(SubscriptionBilling::class)->createOrder($buyer, $starter);
        $payload = $this->payload($order, '00', now()->toIso8601String());

        $this->postJson(route('payments.doku.notification'), $payload, $this->headers($payload))->assertOk();
        $this->postJson(route('payments.doku.notification'), $payload, $this->headers($payload))->assertOk();

        $subscription = Subscription::where('user_id', $buyer->id)->firstOrFail();
        $this->assertSame($starter->id, $subscription->package_id);
        $this->assertSame(1, $subscription->version);
        $this->assertSame('pembeli', $buyer->fresh()->role);
    }

    public function test_invalid_signature_or_amount_does_not_activate_subscription(): void
    {
        $this->configureDoku();
        $this->seed(PackageSeeder::class);
        $buyer = User::factory()->pembeli()->create();
        $order = app(SubscriptionBilling::class)->createOrder($buyer, Package::where('code', 'starter')->firstOrFail());

        $badSignature = $this->payload($order, '00', now()->toIso8601String());
        $headers = $this->headers($badSignature);
        $headers['X-SIGNATURE'] = 'invalid';
        $this->postJson(route('payments.doku.notification'), $badSignature, $headers)->assertUnauthorized();

        $badAmount = $this->payload($order, '00', now()->toIso8601String());
        $badAmount['amount']['value'] = '1.00';
        $this->postJson(route('payments.doku.notification'), $badAmount, $this->headers($badAmount))->assertBadRequest();

        $this->assertDatabaseMissing('subscriptions', ['user_id' => $buyer->id]);
        $this->assertSame('pembeli', $buyer->fresh()->role);
    }

    public function test_late_cancel_notification_cannot_overwrite_paid_subscription_order(): void
    {
        $this->configureDoku();
        $this->seed(PackageSeeder::class);
        $user = User::factory()->pembeli()->create();
        $order = app(SubscriptionBilling::class)->createOrder($user, Package::where('code', 'starter')->firstOrFail());
        $paid = $this->payload($order, '00', now()->toIso8601String());
        $cancelled = $this->payload($order, '05', now()->toIso8601String());

        $this->postJson(route('payments.doku.notification'), $paid, $this->headers($paid))->assertOk();
        $this->postJson(route('payments.doku.notification'), $cancelled, $this->headers($cancelled))->assertOk();

        $this->assertSame('paid', $order->fresh()->status);
        $this->assertTrue($user->fresh()->subscription->isActive());
    }

    public function test_renewal_adds_duration_while_plan_change_preserves_end_date(): void
    {
        $this->seed(PackageSeeder::class);
        $user = User::factory()->pembeli()->create();
        $starter = Package::where('code', 'starter')->firstOrFail();
        $creator = Package::where('code', 'creator')->firstOrFail();
        $billing = app(SubscriptionBilling::class);

        $activation = $billing->createOrder($user, $starter);
        $activation->update(['status' => 'paid', 'provider_paid_at' => now()]);
        $billing->applyPaidOrder($activation, now());
        $firstEnd = $user->fresh()->subscription->ends_at;

        $renewal = $billing->createOrder($user, $starter);
        $renewal->update(['status' => 'paid', 'provider_paid_at' => now()->addMinute()]);
        $billing->applyPaidOrder($renewal, now()->addMinute());
        $renewedEnd = $user->fresh()->subscription->ends_at;
        $this->assertTrue($renewedEnd->equalTo($firstEnd->copy()->addDays(30)));

        $change = $billing->createOrder($user, $creator);
        $change->update(['status' => 'paid', 'provider_paid_at' => now()->addMinutes(2)]);
        $billing->applyPaidOrder($change, now()->addMinutes(2));
        $subscription = $user->fresh()->subscription;
        $this->assertSame($creator->id, $subscription->package_id);
        $this->assertTrue($subscription->ends_at->equalTo($renewedEnd));
    }

    public function test_expired_subscription_keeps_dashboard_read_only_and_hides_listing(): void
    {
        $photographer = User::factory()->fotografer()->create(['is_verified' => true]);
        $photographer->subscription->update(['status' => 'expired', 'ends_at' => now()->subMinute()]);
        $photo = Photo::factory()->for($photographer, 'fotografer')->create(['status' => 'active']);

        $this->actingAs($photographer)->get(route('fotografer.dashboard'))->assertOk()->assertSee('read-only');
        $this->actingAs($photographer)->get(route('fotografer.photos.create'))->assertForbidden();
        $this->get(route('marketplace.show', $photo))->assertNotFound();
        $this->assertSame('active', $photo->fresh()->status);
    }

    public function test_late_plan_change_cannot_replace_a_newer_paid_entitlement(): void
    {
        $this->seed(PackageSeeder::class);
        $user = User::factory()->pembeli()->create();
        $starter = Package::where('code', 'starter')->firstOrFail();
        $creator = Package::where('code', 'creator')->firstOrFail();
        $billing = app(SubscriptionBilling::class);
        $activation = $billing->createOrder($user, $starter);
        $billing->applyPaidOrder($activation, now());
        $olderChange = $billing->createOrder($user, $creator);
        $newerChange = $billing->createOrder($user, $starter);

        $billing->applyPaidOrder($newerChange, now()->addMinutes(2));
        $billing->applyPaidOrder($olderChange, now()->addMinute());

        $this->assertSame($starter->id, $user->fresh()->subscription->package_id);
        $this->assertTrue($olderChange->fresh()->requires_review);
    }

    public function test_expiry_command_marks_elapsed_subscription_without_deleting_data(): void
    {
        $photographer = User::factory()->fotografer()->create();
        $photographer->subscription->update(['status' => 'active', 'ends_at' => now()->subSecond()]);

        $this->artisan('subscriptions:expire')->assertSuccessful();

        $this->assertSame('expired', $photographer->subscription->fresh()->status);
        $this->assertModelExists($photographer);
    }

    public function test_refund_request_is_recorded_without_revoking_subscription(): void
    {
        $this->seed(PackageSeeder::class);
        $user = User::factory()->pembeli()->create();
        $order = app(SubscriptionBilling::class)->createOrder($user, Package::where('code', 'starter')->firstOrFail());
        $order->update(['status' => 'paid']);
        app(SubscriptionBilling::class)->applyPaidOrder($order, now());

        $this->actingAs($user->fresh())->post(route('subscriptions.refunds.store', $order), ['amount' => 29000, 'reason' => 'Permintaan pengembalian dana.'])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('subscription_refunds', ['subscription_order_id' => $order->id, 'status' => 'pending', 'amount' => 29000]);
        $this->assertTrue($user->fresh()->subscription->isActive());
    }

    /** @return array<string, mixed> */
    private function payload(SubscriptionOrder $order, string $status, string $settlementTime): array
    {
        $order->update(['provider_external_id' => '987654321', 'provider_transaction_id' => 'doku-'.$order->id]);

        return [
            'originalPartnerReferenceNo' => $order->order_id,
            'originalReferenceNo' => 'doku-'.$order->id,
            'originalExternalId' => '987654321',
            'latestTransactionStatus' => $status,
            'transactionStatusDesc' => 'Test status',
            'amount' => ['value' => number_format($order->gross_amount, 2, '.', ''), 'currency' => 'IDR'],
            'paidTime' => $settlementTime,
        ];
    }

    private function configureDoku(): void
    {
        config(['doku.client_id' => 'test-client', 'doku.client_secret' => 'test-server-key']);
    }

    /** @param array<string, mixed> $payload
     * @return array<string, string>
     */
    private function headers(array $payload): array
    {
        $timestamp = now()->format('Y-m-d\TH:i:sP');
        $token = 'notification-token';
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        $signature = base64_encode(hash_hmac('sha512', 'POST:/payments/doku/notification:'.$token.':'.hash('sha256', $body).':'.$timestamp, 'test-server-key', true));

        return ['X-PARTNER-ID' => 'test-client', 'X-EXTERNAL-ID' => 'notification-456', 'X-TIMESTAMP' => $timestamp, 'X-SIGNATURE' => $signature, 'Authorization' => 'Bearer '.$token];
    }
}
