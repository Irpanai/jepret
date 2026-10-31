<?php

namespace Tests\Feature;

use App\Models\Photo;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FotograferNetEarningsTest extends TestCase
{
    use RefreshDatabase;

    public function test_net_earnings_are_reduced_only_by_approved_withdrawals(): void
    {
        $photographer = User::factory()->fotografer()->create(['saldo' => 20000]);
        $photo = Photo::factory()->create(['fotografer_id' => $photographer->id]);
        Transaction::factory()->create([
            'photo_id' => $photo->id,
            'fotografer_id' => $photographer->id,
            'status' => 'paid',
            'payment_status' => 'paid',
            'harga_foto' => 50000,
            'tip_amount' => 0,
            'total_bayar' => 50000,
            'photographer_amount' => 50000,
            'paid_at' => now(),
        ]);

        Withdrawal::factory()->for($photographer, 'fotografer')->create(['status' => 'success', 'jumlah_tarik' => 10000]);
        Withdrawal::factory()->for($photographer, 'fotografer')->create(['status' => 'pending', 'jumlah_tarik' => 15000]);
        Withdrawal::factory()->for($photographer, 'fotografer')->create(['status' => 'held', 'jumlah_tarik' => 5000]);
        Withdrawal::factory()->for($photographer, 'fotografer')->create(['status' => 'rejected', 'jumlah_tarik' => 7000, 'admin_notes' => 'Rekening tidak valid']);

        $response = $this->actingAs($photographer)->get(route('fotografer.dashboard'));

        $response->assertOk();
        $response->assertViewHas('stats', fn (array $stats): bool => $stats['earnings'] === 40000
            && $stats['gross_earnings'] === 50000
            && $stats['withdrawn'] === 10000);
        $response->assertViewHas('withdrawalSummary', fn (array $summary): bool => $summary['success_amount'] === 10000
            && $summary['success_count'] === 1
            && $summary['processing_amount'] === 20000
            && $summary['processing_count'] === 2
            && $summary['rejected_amount'] === 7000
            && $summary['rejected_count'] === 1);
        $response->assertSee('Riwayat Penarikan');
        $response->assertSee('Rekening tidak valid');
    }

    public function test_net_earnings_never_go_below_zero(): void
    {
        $photographer = User::factory()->fotografer()->create();
        Withdrawal::factory()->for($photographer, 'fotografer')->create(['status' => 'success', 'jumlah_tarik' => 30000]);

        $response = $this->actingAs($photographer)->get(route('fotografer.dashboard'));

        $response->assertOk();
        $response->assertViewHas('stats', fn (array $stats): bool => $stats['earnings'] === 0);
    }

    public function test_withdrawal_history_is_paginated_and_masks_destination(): void
    {
        $photographer = User::factory()->fotografer()->create();
        Withdrawal::factory()->count(6)->for($photographer, 'fotografer')->create([
            'status' => 'pending',
            'nomor_tujuan' => '1234567890',
        ]);

        $response = $this->actingAs($photographer)->get(route('fotografer.dashboard'));

        $response->assertOk();
        $response->assertViewHas('withdrawals', fn ($withdrawals): bool => $withdrawals->count() === 5 && $withdrawals->total() === 6);
        $response->assertSee('****7890');
        $response->assertDontSee('1234567890');
    }
}
