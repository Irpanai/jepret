<?php

namespace Tests\Feature;

use App\Models\Photo;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use App\PhotographerNotificationFeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhotographerNotificationFeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_feed_includes_sales_and_withdrawals(): void
    {
        $photographer = User::factory()->fotografer()->create([
            'is_verified' => true,
            'verified_at' => now()->subDays(5),
        ]);

        $photo = Photo::factory()->create(['fotografer_id' => $photographer->id, 'title' => 'Pantai Kuta']);
        Transaction::factory()->create([
            'photo_id' => $photo->id,
            'fotografer_id' => $photographer->id,
            'status' => 'paid',
            'payment_status' => 'paid',
            'photographer_amount' => 45000,
            'paid_at' => now()->subMinutes(10),
        ]);

        Withdrawal::factory()->for($photographer, 'fotografer')->create([
            'status' => 'success',
            'jumlah_tarik' => 50000,
            'metode_pembayaran' => 'BCA',
        ]);

        $feed = PhotographerNotificationFeed::forUser($photographer);

        $this->assertNotEmpty($feed);
        $this->assertTrue(collect($feed)->contains(fn (array $item): bool => $item['type'] === 'sale' && str_contains($item['message'], 'Pantai Kuta')));
        $this->assertTrue(collect($feed)->contains(fn (array $item): bool => $item['type'] === 'withdrawal' && $item['status'] === 'success'));
        $this->assertTrue(collect($feed)->contains(fn (array $item): bool => $item['type'] === 'verification'));
    }

    public function test_fg_layout_renders_with_notification_dropdown(): void
    {
        $photographer = User::factory()->fotografer()->create();

        $response = $this->actingAs($photographer)->get(route('fotografer.dashboard'));

        $response->assertOk();
        $response->assertSee('Notifikasi');
        $response->assertSee('Tandai dibaca');
    }
}

