<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_only_offers_photographer_registration(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('Nama studio')
            ->assertSee('Nomor WhatsApp')
            ->assertSee('Buat akun Photographer')
            ->assertDontSee('Lanjutkan dengan Google')
            ->assertDontSee('name="role"', false);
    }

    public function test_guest_can_register_as_photographer_and_is_sent_to_subscription_plans(): void
    {
        $response = $this->post('/register', $this->payload());

        $user = User::where('email', 'test@example.com')->sole();
        $this->assertAuthenticatedAs($user);
        $this->assertTrue($user->hasRole('pembeli'));
        $this->assertTrue($user->hasRole('fotografer'));
        $this->assertSame('Studio Test', $user->studio_name);
        $this->assertSame('6281234567890', $user->whatsapp);
        $this->assertTrue($user->is_verified);
        $this->assertNotNull($user->photographer_onboarded_at);
        $this->assertDatabaseCount('subscriptions', 0);
        $response->assertRedirect(route('subscriptions.plans', absolute: false));
    }

    public function test_duplicate_email_is_rejected_without_creating_an_account(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $this->from('/register')->post('/register', $this->payload())
            ->assertRedirect('/register')
            ->assertSessionHasErrors('email');

        $this->assertSame(1, User::count());
    }

    public function test_invalid_registration_input_is_rejected(): void
    {
        $this->post('/register', [
            'name' => '',
            'studio_name' => '',
            'whatsapp' => 'abc',
            'email' => 'invalid',
            'password' => 'short',
            'password_confirmation' => 'different',
        ])->assertSessionHasErrors(['name', 'studio_name', 'whatsapp', 'email', 'password']);

        $this->assertDatabaseCount('users', 0);
    }

    public function test_buyer_adds_photographer_access_to_the_same_account(): void
    {
        $buyer = User::factory()->pembeli()->create([
            'email' => 'buyer@example.com',
            'name' => 'Buyer Lama',
            'studio_name' => null,
            'whatsapp' => null,
        ]);

        $response = $this->actingAs($buyer)->post('/register', [
            'name' => 'Buyer Photographer',
            'studio_name' => 'Studio Buyer',
            'whatsapp' => '0812 3456 7890',
            'email' => 'attacker@example.com',
            'password' => 'password-baru',
        ]);

        $buyer->refresh();
        $this->assertSame(1, User::count());
        $this->assertSame('buyer@example.com', $buyer->email);
        $this->assertTrue($buyer->hasRole('pembeli'));
        $this->assertTrue($buyer->hasRole('fotografer'));
        $this->assertSame('Studio Buyer', $buyer->studio_name);
        $response->assertRedirect(route('subscriptions.plans', absolute: false));
    }

    public function test_repeated_activation_does_not_duplicate_or_overwrite_the_profile(): void
    {
        $photographer = User::factory()->fotografer()->create([
            'studio_name' => 'Studio Asli',
            'whatsapp' => '628111111111',
            'photographer_onboarded_at' => null,
        ]);

        $response = $this->actingAs($photographer)->post('/register', [
            'name' => 'Nama Penyerang',
            'studio_name' => 'Studio Penyerang',
            'whatsapp' => '081222222222',
        ]);

        $photographer->refresh();
        $this->assertSame(1, User::count());
        $this->assertSame('Studio Asli', $photographer->studio_name);
        $this->assertSame('628111111111', $photographer->whatsapp);
        $response->assertRedirect(route('fotografer.dashboard', absolute: false));
    }

    public function test_completed_onboarding_is_not_repeated(): void
    {
        $photographer = User::factory()->fotografer()->create();

        $this->actingAs($photographer)->get('/register')
            ->assertRedirect(route('fotografer.dashboard', absolute: false));
        $this->actingAs($photographer)->get(route('fotografer.onboarding'))
            ->assertRedirect(route('fotografer.dashboard', absolute: false));
    }

    public function test_buyer_cannot_access_photographer_routes_before_activation(): void
    {
        $buyer = User::factory()->pembeli()->create();

        $this->actingAs($buyer)->get(route('fotografer.dashboard'))->assertForbidden();
    }

    public function test_admin_role_remains_compatible(): void
    {
        $admin = User::factory()->superadmin()->create();

        $this->assertTrue($admin->hasRole('superadmin'));
        $this->assertFalse($admin->hasRole('fotografer'));
        $this->actingAs($admin)->get(route('dashboard'))
            ->assertRedirect(route('superadmin.dashboard', absolute: false));
    }

    public function test_onboarding_completion_is_saved(): void
    {
        $photographer = User::factory()->fotografer()->create(['photographer_onboarded_at' => null]);

        $this->actingAs($photographer)->post(route('fotografer.onboarding.store'))
            ->assertRedirect(route('subscriptions.plans', absolute: false));

        $this->assertNotNull($photographer->fresh()->photographer_onboarded_at);
    }

    public function test_failed_registration_rolls_back_the_account_and_profile(): void
    {
        DB::statement("CREATE TRIGGER fail_photographer_profile BEFORE UPDATE OF studio_name ON users BEGIN SELECT RAISE(FAIL, 'profile failed'); END");

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.99'])
            ->post('/register', $this->payload())
            ->assertServerError();

        $this->assertDatabaseCount('users', 0);
    }

    /** @return array<string, string> */
    private function payload(): array
    {
        return [
            'name' => 'Test Photographer',
            'studio_name' => 'Studio Test',
            'whatsapp' => '0812-3456-7890',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];
    }
}
