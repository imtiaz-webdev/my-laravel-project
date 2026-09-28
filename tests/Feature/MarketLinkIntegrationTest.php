<?php

namespace Tests\Feature;

use App\Models\FarmerProfile;
use App\Models\Market;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketLinkIntegrationTest extends TestCase
{
    public function test_homepage_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('MarketLink');
        $response->assertSee('Zero-Waste Harvest Bundles');
    }

    public function test_market_explorer_loads(): void
    {
        $response = $this->get('/explorer');
        $response->assertStatus(200);
        $response->assertSee('marketMap');
    }

    public function test_faq_bot_endpoint(): void
    {
        $response = $this->postJson('/api/faq-bot', [
            'message' => 'Tell me about zero waste discounts'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['reply']);
    }

    public function test_legal_pages_render(): void
    {
        $tos = $this->get('/terms-of-service');
        $tos->assertStatus(200);
        $tos->assertSee('Terms of Service');

        $privacy = $this->get('/privacy-policy');
        $privacy->assertStatus(200);
        $privacy->assertSee('Privacy Policy');
    }

    public function test_admin_routes_require_authentication(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
        $this->get('/admin/products')->assertRedirect('/login');
        $this->get('/demo-switch/admin')->assertNotFound();
    }

    public function test_login_page_renders_without_demo_credentials(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertDontSee('Demo Pass')
            ->assertDontSee('Instant Direct Switch')
            ->assertDontSee('admin@marketlink.org');
    }

    public function test_wrong_password_cannot_authenticate_an_admin(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->from('/login')->post('/login', [
            'email' => $admin->email,
            'password' => 'incorrect-password',
        ])->assertRedirect('/login')->assertSessionHasErrors('email');

        $this->get('/admin/dashboard')->assertRedirect('/login');
    }

    public function test_non_admin_users_are_forbidden_from_admin_routes(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
        ]);

        $this->actingAs($customer)->get('/admin/dashboard')->assertForbidden();
    }

    public function test_customer_dashboard_and_route_optimizer(): void
    {
        $customer = User::where('role', 'customer')->first();
        $this->actingAs($customer);

        $dash = $this->get('/customer/dashboard');
        $dash->assertStatus(200);
        $dash->assertSee('Pre-Orders');

        $route = $this->get('/customer/pickup-route');
        $route->assertStatus(200);
        $route->assertSee('routeMap');
    }

    public function test_vendor_dashboard_and_pos_qr_verification(): void
    {
        $farmerUser = User::where('role', 'vendor')->first();
        $this->actingAs($farmerUser);

        $dash = $this->get('/vendor/dashboard');
        $dash->assertStatus(200);
        $dash->assertSee('Gross Cash Volume');

        $inv = $this->get('/vendor/inventory');
        $inv->assertStatus(200);
        $inv->assertSee('Inventory & Stock Matrix');

        // Test QR token POS verification
        $order = Order::where('farmer_profile_id', $farmerUser->farmerProfile->id)->first();
        if ($order) {
            $order->update(['status' => 'ready']);
            $verifyRes = $this->post('/vendor/verify-qr', [
                'pickup_token' => $order->pickup_token
            ]);
            $verifyRes->assertSessionHas('success');
            $this->assertEquals('completed', $order->fresh()->status);
        }
    }

    public function test_admin_dashboard_and_csv_export(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $dash = $this->get('/admin/dashboard');
        $dash->assertStatus(200);
        $dash->assertSee('Directorate');

        $farmers = $this->get('/admin/farmers');
        $farmers->assertStatus(200);
        $farmers->assertSee('Approval Engine');

        $csv = $this->get('/admin/reports/export-csv');
        $csv->assertStatus(200);
    }
}
