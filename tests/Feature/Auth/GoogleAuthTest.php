<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_redirect_to_google(): void
    {
        $response = $this->get(route('auth.google.redirect'));

        $response->assertRedirect();
        $this->assertStringContainsString('accounts.google.com', $response->getTargetUrl());
    }

    public function test_photographer_intent_does_not_grant_role_before_subscription_activation(): void
    {
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getId')->andReturn('google-12345');
        $abstractUser->shouldReceive('getEmail')->andReturn('fotografer@example.com');
        $abstractUser->shouldReceive('getName')->andReturn('Budi Fotografer');
        $abstractUser->shouldReceive('getNickname')->andReturn(null);
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar.jpg');

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->withSession(['google_register_role' => 'fotografer'])
            ->get(route('auth.google.callback'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'fotografer@example.com',
            'google_id' => 'google-12345',
            'role' => 'pembeli',
        ]);
        $response->assertRedirect(route('galeri', absolute: false));
    }

    public function test_google_login_does_not_link_an_existing_local_account_by_email(): void
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'role' => 'pembeli',
            'google_id' => null,
        ]);

        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getId')->andReturn('google-67890');
        $abstractUser->shouldReceive('getEmail')->andReturn('existing@example.com');
        $abstractUser->shouldReceive('getName')->andReturn('Existing User');
        $abstractUser->shouldReceive('getNickname')->andReturn(null);
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://avatar.url');

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $this->assertGuest();
        $this->assertNull($user->fresh()->google_id);
        $response->assertRedirect(route('login', absolute: false));
        $response->assertSessionHasErrors('email');
    }

    public function test_linked_photographer_keeps_all_roles_after_google_login(): void
    {
        $photographer = User::factory()->fotografer()->create([
            'google_id' => 'google-photographer',
        ]);

        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getId')->andReturn('google-photographer');
        $abstractUser->shouldReceive('getEmail')->andReturn($photographer->email);
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://avatar.url');

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')->andReturn($abstractUser);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $this->assertAuthenticatedAs($photographer);
        $this->assertTrue($photographer->fresh()->hasRole('pembeli'));
        $this->assertTrue($photographer->fresh()->hasRole('fotografer'));
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
