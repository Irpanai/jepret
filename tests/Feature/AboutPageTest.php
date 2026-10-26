<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_renders_editorial_sections_and_valid_ctas_for_guests(): void
    {
        $response = $this->get(route('about'));

        $response->assertOk()
            ->assertSee('Every moment.')
            ->assertSee('One place.')
            ->assertSee('Jepret mempertemukan orang yang mencari momen dengan fotografer yang menangkapnya.')
            ->assertSee('The best moments are already out there.')
            ->assertSee('We make them easier to find.')
            ->assertSee('Discover')
            ->assertSee('Choose')
            ->assertSee('Keep')
            ->assertSee('Your work deserves to be')
            ->assertSee('Your moment is worth')
            ->assertSee('A photograph is more than an image.')
            ->assertSee('The moment happened.')
            ->assertSee('Now find it.')
            ->assertSee(route('galeri'), false)
            ->assertSee(route('photographers.index'), false)
            ->assertSee(route('pricing'), false)
            ->assertSee(route('register', ['role' => 'fotografer']), false);
    }

    public function test_about_page_renders_photographer_dashboard_cta_for_authenticated_photographer(): void
    {
        $photographer = User::factory()->fotografer()->create();

        $response = $this->actingAs($photographer)->get(route('about'));

        $response->assertOk()
            ->assertSee(route('fotografer.dashboard'), false)
            ->assertSee(route('cart.index'), false)
            ->assertSee(route('purchases.index'), false)
            ->assertSee('Halaman Saya')
            ->assertSee('Pembelian Saya');
    }

    public function test_navbar_renders_all_primary_navigation_routes_and_mobile_drawer(): void
    {
        $response = $this->get(route('about'));

        $response->assertOk()
            ->assertSee('aria-label="Navigasi utama"', false)
            ->assertSee('id="mobile-navigation"', false)
            ->assertSee('aria-current="page"', false)
            ->assertSee(route('landing'), false)
            ->assertSee(route('about'), false)
            ->assertSee(route('galeri'), false)
            ->assertSee(route('photographers.index'), false)
            ->assertSee(route('pricing'), false)
            ->assertSee(route('login'), false)
            ->assertSee(route('register'), false);
    }
}

