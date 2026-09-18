<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Photo;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MarketplaceFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_add_active_photo_to_cart(): void
    {
        $buyer = User::factory()->pembeli()->create();
        $photo = $this->createMarketplacePhoto();

        $response = $this->actingAs($buyer)->post(route('cart.store'), [
            'photo_id' => $photo->id,
        ]);

        $response->assertRedirect();
        $this->assertSame([$photo->id => $photo->id], session('cart'));
    }

    public function test_guest_is_redirected_to_login_when_adding_photo_to_cart(): void
    {
        $photo = $this->createMarketplacePhoto();

        $this->post(route('cart.store'), ['photo_id' => $photo->id])
            ->assertRedirect(route('login'));

        $this->assertNull(session('cart'));
    }

    public function test_authenticated_cart_request_can_return_realtime_count(): void
    {
        $buyer = User::factory()->pembeli()->create();
        $photo = $this->createMarketplacePhoto();

        $this->actingAs($buyer)
            ->postJson(route('cart.store'), ['photo_id' => $photo->id])
            ->assertOk()
            ->assertJson([
                'message' => 'Ditambahkan ke keranjang',
                'cart_count' => 1,
                'photo_id' => $photo->id,
            ]);

        $this->assertSame([$photo->id => $photo->id], session('cart'));
    }

    public function test_checkout_creates_pending_transactions_with_90_10_snapshot(): void
    {
        $buyer = User::factory()->pembeli()->create();
        $photographer = User::factory()->fotografer()->create(['saldo' => 0, 'is_verified' => true, 'verified_at' => now()]);
        $photo = $this->createMarketplacePhoto($photographer, ['harga' => 25000]);

        $response = $this->actingAs($buyer)
            ->withSession(['cart' => [$photo->id => $photo->id]])
            ->post(route('checkout.process'));

        $transaction = Transaction::firstOrFail();

        $response->assertRedirect(route('checkout.payment', ['order' => $transaction->order_number], absolute: false));
        $this->assertSame('pending', $transaction->status);
        $this->assertSame(22500, $transaction->photographer_amount);
        $this->assertSame(2500, $transaction->platform_amount);
        $this->assertSame(90, $transaction->revenue_share_snapshot['photographer_percent']);
        $this->assertSame(10, $transaction->revenue_share_snapshot['platform_percent']);
    }

    public function test_multi_item_checkout_keeps_cart_until_every_item_is_paid(): void
    {
        $buyer = User::factory()->pembeli()->create();
        $firstPhoto = $this->createMarketplacePhoto(attributes: ['harga' => 20000]);
        $secondPhoto = $this->createMarketplacePhoto(attributes: ['harga' => 35000]);
        $cart = [$firstPhoto->id => $firstPhoto->id, $secondPhoto->id => $secondPhoto->id];

        $response = $this->actingAs($buyer)
            ->withSession(['cart' => $cart])
            ->post(route('checkout.process'));

        $transactions = Transaction::orderBy('id')->get();
        $this->assertCount(2, $transactions);
        $this->assertSame(1, $transactions->pluck('order_number')->unique()->count());
        $this->assertSame([$firstPhoto->id, $secondPhoto->id], $transactions->pluck('photo_id')->sort()->values()->all());
        $this->assertSame($cart, session('cart'));
        $response->assertRedirect(route('checkout.payment', ['order' => $transactions->first()->order_number], absolute: false));

        $this->actingAs($buyer)
            ->withSession(['cart' => $cart])
            ->post(route('checkout.payment.simulate', ['order' => $transactions->first()->order_number]))
            ->assertRedirect(route('checkout.success', ['order' => $transactions->first()->order_number]));

        $this->assertNull(session('cart'));
        $this->assertSame(2, Transaction::where('payment_status', 'paid')->count());
    }

    public function test_checkout_does_not_clear_cart_or_create_partial_order_when_an_item_is_unavailable(): void
    {
        $buyer = User::factory()->pembeli()->create();
        $availablePhoto = $this->createMarketplacePhoto();
        $unavailablePhoto = $this->createMarketplacePhoto(attributes: ['status' => 'inactive']);
        $cart = [$availablePhoto->id => $availablePhoto->id, $unavailablePhoto->id => $unavailablePhoto->id];

        $this->actingAs($buyer)
            ->withSession(['cart' => $cart])
            ->post(route('checkout.process'))
            ->assertRedirect(route('cart.index'))
            ->assertSessionHasErrors('cart');

        $this->assertSame($cart, session('cart'));
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_purchased_variant_download_requires_paid_buyer_transaction(): void
    {
        Storage::fake('local');

        $buyer = User::factory()->pembeli()->create();
        $otherBuyer = User::factory()->pembeli()->create();
        $photo = $this->createMarketplacePhoto(attributes: [
            'file_asli' => 'photos/original/private-image.jpg',
            'purchased_path' => 'photos/purchased/private-image.jpg',
            'original_filename' => 'private-image.jpg',
        ]);

        Storage::disk('local')->put($photo->file_asli, 'original-bytes');
        Storage::disk('local')->put($photo->purchased_path, 'photographer-watermarked-bytes');

        $transaction = Transaction::factory()->create([
            'pembeli_id' => $buyer->id,
            'photo_id' => $photo->id,
            'order_number' => 'JEPRET-TEST-001',
            'status' => 'pending',
            'payment_status' => 'pending',
            'harga_foto' => $photo->harga,
            'total_bayar' => $photo->harga,
        ]);

        $this->actingAs($buyer)
            ->get(route('purchases.download', ['order' => $transaction->order_number, 'transaction' => $transaction]))
            ->assertForbidden();

        $transaction->update([
            'status' => 'paid',
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        $this->actingAs($otherBuyer)
            ->get(route('purchases.download', ['order' => $transaction->order_number, 'transaction' => $transaction]))
            ->assertForbidden();

        $this->actingAs($buyer)
            ->get(route('purchases.download', ['order' => $transaction->order_number, 'transaction' => $transaction]))
            ->assertDownload('private-image.jpg');

        $preview = $this->actingAs($buyer)
            ->get(route('purchases.preview', ['order' => $transaction->order_number, 'transaction' => $transaction]));
        $preview->assertOk();
        $this->assertSame('photographer-watermarked-bytes', $preview->streamedContent());
    }

    public function test_paid_order_redirects_to_success_and_is_visible_in_purchases(): void
    {
        $buyer = User::factory()->pembeli()->create();
        $photo = $this->createMarketplacePhoto();
        $orderNumber = 'JEPRET-TEST-PAID';

        $transaction = Transaction::factory()->create([
            'order_number' => $orderNumber,
            'pembeli_id' => $buyer->id,
            'photo_id' => $photo->id,
            'status' => 'pending',
            'payment_status' => 'pending',
            'harga_foto' => $photo->harga,
            'total_bayar' => $photo->harga,
        ]);

        $this->actingAs($buyer)
            ->post(route('checkout.payment.simulate', ['order' => $orderNumber]))
            ->assertRedirect(route('checkout.success', ['order' => $orderNumber], absolute: false));

        $transaction->refresh();
        $this->assertSame('paid', $transaction->status);
        $this->assertSame('paid', $transaction->payment_status);

        $this->actingAs($buyer)
            ->get(route('checkout.success', ['order' => $orderNumber]))
            ->assertOk()
            ->assertSee('Pembayaran Berhasil')
            ->assertSee($orderNumber);

        $this->actingAs($buyer)
            ->get(route('purchases.index'))
            ->assertOk()
            ->assertSee('Pembelian Saya')
            ->assertSee($orderNumber);
    }

    public function test_other_buyer_cannot_open_purchase_order_detail(): void
    {
        $buyer = User::factory()->pembeli()->create();
        $otherBuyer = User::factory()->pembeli()->create();
        $photo = $this->createMarketplacePhoto();
        $orderNumber = 'JEPRET-OWNER-ONLY';

        Transaction::factory()->create([
            'order_number' => $orderNumber,
            'pembeli_id' => $buyer->id,
            'photo_id' => $photo->id,
            'status' => 'paid',
            'payment_status' => 'paid',
            'harga_foto' => $photo->harga,
            'total_bayar' => $photo->harga,
        ]);

        $this->actingAs($otherBuyer)
            ->get(route('purchases.show', ['order' => $orderNumber]))
            ->assertNotFound();
    }

    public function test_unpaid_failed_and_expired_orders_cannot_download_purchased_variant(): void
    {
        Storage::fake('local');

        $buyer = User::factory()->pembeli()->create();
        $photo = $this->createMarketplacePhoto(attributes: [
            'file_asli' => 'photos/original/restricted-image.jpg',
            'purchased_path' => 'photos/purchased/restricted-image.jpg',
            'original_filename' => 'restricted-image.jpg',
        ]);

        Storage::disk('local')->put($photo->file_asli, 'original-bytes');
        Storage::disk('local')->put($photo->purchased_path, 'photographer-watermarked-bytes');

        foreach (['pending', 'failed', 'expired'] as $status) {
            $transaction = Transaction::factory()->create([
                'order_number' => 'JEPRET-'.strtoupper($status),
                'pembeli_id' => $buyer->id,
                'photo_id' => $photo->id,
                'status' => $status === 'pending' ? 'pending' : 'paid',
                'payment_status' => $status,
                'harga_foto' => $photo->harga,
                'total_bayar' => $photo->harga,
            ]);

            $this->actingAs($buyer)
                ->get(route('purchases.download', ['order' => $transaction->order_number, 'transaction' => $transaction]))
                ->assertForbidden();
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createMarketplacePhoto(?User $photographer = null, array $attributes = []): Photo
    {
        $photographer ??= User::factory()->fotografer()->create(['is_verified' => true, 'verified_at' => now()]);
        $event = Event::factory()->create(['fotografer_id' => $photographer->id]);

        return Photo::factory()->create(array_merge([
            'event_id' => $event->id,
            'fotografer_id' => $photographer->id,
            'status' => 'active',
            'published_at' => now(),
            'harga' => 20000,
        ], $attributes));
    }
}
