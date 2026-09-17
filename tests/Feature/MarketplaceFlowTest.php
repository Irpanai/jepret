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

    public function test_checkout_creates_pending_transactions_with_90_10_snapshot(): void
    {
        $buyer = User::factory()->pembeli()->create();
        $photographer = User::factory()->fotografer()->create(['saldo' => 0]);
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

    public function test_original_download_requires_paid_buyer_transaction(): void
    {
        Storage::fake('local');

        $buyer = User::factory()->pembeli()->create();
        $otherBuyer = User::factory()->pembeli()->create();
        $photo = $this->createMarketplacePhoto(attributes: [
            'file_asli' => 'photos/original/private-image.jpg',
            'original_filename' => 'private-image.jpg',
        ]);

        Storage::disk('local')->put($photo->file_asli, 'original-bytes');

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

    public function test_unpaid_failed_and_expired_orders_cannot_download_original(): void
    {
        Storage::fake('local');

        $buyer = User::factory()->pembeli()->create();
        $photo = $this->createMarketplacePhoto(attributes: [
            'file_asli' => 'photos/original/restricted-image.jpg',
            'original_filename' => 'restricted-image.jpg',
        ]);

        Storage::disk('local')->put($photo->file_asli, 'original-bytes');

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
        $photographer ??= User::factory()->fotografer()->create();
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
