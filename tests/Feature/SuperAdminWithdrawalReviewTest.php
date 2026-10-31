<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminWithdrawalReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_withdrawal_review_route_redirects_gracefully_to_earnings(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $response = $this->actingAs($superadmin)
            ->get('/superadmin/withdrawals/20/review');

        $response->assertRedirect(route('superadmin.earnings', ['q' => 20]));
    }

    public function test_get_withdrawals_redirects_to_earnings(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $response = $this->actingAs($superadmin)
            ->get('/superadmin/withdrawals');

        $response->assertRedirect(route('superadmin.earnings'));
    }

    public function test_get_single_withdrawal_redirects_to_earnings_with_filter(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $response = $this->actingAs($superadmin)
            ->get('/superadmin/withdrawals/42');

        $response->assertRedirect(route('superadmin.earnings', ['q' => 42]));
    }

    public function test_post_withdrawal_review_can_hold_withdrawal(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        $photographer = User::factory()->fotografer()->create(['saldo' => 500000]);
        $withdrawal = Withdrawal::factory()->create([
            'fotografer_id' => $photographer->id,
            'status' => 'pending',
            'jumlah_tarik' => 100000,
        ]);

        $response = $this->actingAs($superadmin)
            ->post(route('superadmin.withdrawals.review', $withdrawal), [
                'status' => 'held',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('withdrawals', [
            'id' => $withdrawal->id,
            'status' => 'held',
        ]);
    }

    public function test_earnings_search_can_filter_by_withdrawal_id(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        $photographer = User::factory()->fotografer()->create();
        $withdrawal1 = Withdrawal::factory()->create([
            'fotografer_id' => $photographer->id,
            'status' => 'pending',
            'jumlah_tarik' => 100000,
        ]);
        $withdrawal2 = Withdrawal::factory()->create([
            'fotografer_id' => $photographer->id,
            'status' => 'pending',
            'jumlah_tarik' => 200000,
        ]);

        $response = $this->actingAs($superadmin)
            ->get(route('superadmin.earnings', ['q' => $withdrawal1->id]));

        $response->assertOk();
        $response->assertSee('#WD-'.$withdrawal1->id);
    }
}
