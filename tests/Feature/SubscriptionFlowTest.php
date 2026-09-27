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

    public function test_trial_can_be_activated_once_and_promotes_buyer_to_photographer(): void
    {
        $this->seed(PackageSeeder::class);
        $buyer = User::factory()->pembeli()->create();
        $trial = Package::where('code', 'trial')->firstOrFail();

        $this->actingAs($buyer)->post(route('subscriptions.checkout', $trial))->assertRedirect(route('fotografer.dashboard'));

        $buyer->refresh()->load('subscription');
        $this->assertSame('fotografer', $buyer->role);
        $this->assertTrue($buyer->subscription->isActive());
        $this->assertNotNull($buyer->subscription->trial_used_at);

        $this->actingAs($buyer)->post(route('subscriptions.checkout', $trial))->assertSessionHasErrors('trial');
        $this->assertSame(1, SubscriptionOrder::where('intent', 'trial')->count());
    }

    public function test_valid_webhook_activates_subscription_once_and_duplicate_is_idempotent(): void
    {
        config(['midtrans.server_key' => 'test-server-key']);
        $this->seed(PackageSeeder::class);
        $buyer = User::factory()->pembeli()->create();
        $starter = Package::where('code', 'starter')->firstOrFail();
        $order = app(SubscriptionBilling::class)->createOrder($buyer, $starter);
        $payload = $this->payload($order, 'settlement', '2026-09-26 10:00:00');

        $this->postJson(route('payments.midtrans.notification'), $payload)->assertOk()->assertJson(['result' => 'paid']);
        $this->postJson(route('payments.midtrans.notification'), $payload)->assertOk()->assertJson(['result' => 'duplicate']);

        $subscription = Subscription::where('user_id', $buyer->id)->firstOrFail();
        $this->assertSame($starter->id, $subscription->package_id);
        $this->assertSame(1, $subscription->version);
        $this->assertSame('fotografer', $buyer->fresh()->role);
    }

    public function test_invalid_signature_or_amount_does_not_activate_subscription(): void
    {
        config(['midtrans.server_key' => 'test-server-key']);
        $this->seed(PackageSeeder::class);
        $buyer = User::factory()->pembeli()->create();
        $order = app(SubscriptionBilling::class)->createOrder($buyer, Package::where('code', 'starter')->firstOrFail());

        $badSignature = $this->payload($order, 'settlement', '2026-09-26 10:00:00');
        $badSignature['signature_key'] = 'invalid';
        $this->postJson(route('payments.midtrans.notification'), $badSignature)->assertUnprocessable();

        $badAmount = $this->payload($order, 'settlement', '2026-09-26 10:01:00');
        $badAmount['gross_amount'] = '1.00';
        $badAmount['signature_key'] = $this->signature($badAmount);
        $this->postJson(route('payments.midtrans.notification'), $badAmount)->assertUnprocessable();

        $this->assertDatabaseMissing('subscriptions', ['user_id' => $buyer->id]);
        $this->assertSame('pembeli', $buyer->fresh()->role);
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

    /** @return array<string, string> */
    private function payload(SubscriptionOrder $order, string $status, string $settlementTime): array
    {
        $payload = [
            'order_id' => $order->order_id,
            'status_code' => '200',
            'gross_amount' => number_format($order->gross_amount, 2, '.', ''),
            'transaction_status' => $status,
            'transaction_id' => 'midtrans-'.$order->id,
            'payment_type' => 'qris',
            'fraud_status' => 'accept',
            'settlement_time' => $settlementTime,
        ];
        $payload['signature_key'] = $this->signature($payload);

        return $payload;
    }

    /** @param array<string, string> $payload */
    private function signature(array $payload): string
    {
        return hash('sha512', $payload['order_id'].$payload['status_code'].$payload['gross_amount'].config('midtrans.server_key'));
    }
}
