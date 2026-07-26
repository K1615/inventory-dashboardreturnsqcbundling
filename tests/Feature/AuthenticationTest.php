<?php

namespace Tests\Feature;

use App\Http\Controllers\AuthController;
use App\Models\InventoryRequest;
use App\Models\User;
use Database\Seeders\DemoUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_login_page(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Welcome back')
            ->assertSee('Remember me');
    }

    public function test_authenticated_user_is_redirected_away_from_login_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/login')
            ->assertRedirect('/');
    }

    public function test_user_can_log_in_and_is_sent_to_intended_page(): void
    {
        $user = User::factory()->create([
            'email' => 'employee@gmail.com',
            'password' => Hash::make('admin123'),
        ]);

        $this->get('/warehouse')->assertRedirect('/login');

        $this->post('/login', [
            'email' => 'employee@gmail.com',
            'password' => 'admin123',
        ])->assertRedirect('/warehouse');

        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_credentials_use_a_generic_error(): void
    {
        User::factory()->create([
            'email' => 'employee@gmail.com',
            'password' => Hash::make('admin123'),
        ]);

        $this->from('/login')->post('/login', [
            'email' => 'employee@gmail.com',
            'password' => 'incorrect-password',
        ])->assertRedirect('/login')
            ->assertSessionHasErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);

        $this->assertGuest();
    }

    public function test_login_attempts_are_throttled(): void
    {
        User::factory()->create([
            'email' => 'employee@gmail.com',
            'password' => Hash::make('admin123'),
        ]);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/login', [
                'email' => 'employee@gmail.com',
                'password' => 'incorrect-password',
            ]);
        }

        $this->post('/login', [
            'email' => 'employee@gmail.com',
            'password' => 'incorrect-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_authenticated_user_can_log_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/login');

        $this->assertGuest();
    }

    public function test_guest_is_redirected_from_every_erp_page(): void
    {
        foreach (['/', '/movement', '/warehouse', '/alerts', '/items'] as $uri) {
            $this->get($uri)->assertRedirect('/login');
        }
    }

    public function test_unauthenticated_ajax_and_api_requests_receive_unauthorized_response(): void
    {
        $requests = [
            ['getJson', '/inventory/api/state'],
            ['postJson', '/inventory/api/inspection'],
            ['getJson', '/api/stock-movements/data'],
            ['postJson', '/warehouse-layout/request'],
            ['postJson', '/api/requests'],
            ['getJson', '/api/v1/inventory/state'],
        ];

        foreach ($requests as [$method, $uri]) {
            $response = $this->{$method}($uri);

            $this->assertSame(401, $response->getStatusCode(), "{$uri} did not return 401.");
        }
    }

    public function test_every_erp_controller_route_has_auth_middleware(): void
    {
        foreach (app('router')->getRoutes() as $route) {
            $action = $route->getActionName();

            if (! str_starts_with($action, 'App\\Http\\Controllers\\')
                || str_starts_with($action, AuthController::class.'@')) {
                continue;
            }

            $this->assertContains(
                'auth',
                $route->gatherMiddleware(),
                "Route [{$route->uri()}] is missing auth middleware.",
            );
        }

        $logout = app('router')->getRoutes()->getByName('logout');
        $this->assertNotNull($logout);
        $this->assertContains('auth', $logout->gatherMiddleware());
    }

    public function test_authenticated_user_can_access_erp_pages(): void
    {
        $this->actingAs(User::factory()->create());

        foreach (['/', '/movement', '/warehouse', '/alerts', '/items'] as $uri) {
            $this->get($uri)->assertOk();
        }
    }

    public function test_mutations_use_authenticated_identity_instead_of_spoofed_input(): void
    {
        $user = User::factory()->create(['name' => 'Authenticated Employee']);

        $this->actingAs($user)
            ->postJson('/api/requests', [
                'type' => 'ADD',
                'requestor' => 'Spoofed User',
                'target_item_id' => null,
                'proposed_data' => [
                    'name' => 'Test Item',
                    'category' => 'Storage',
                    'qty' => 1,
                    'price' => 10,
                    'warehouse' => 'Warehouse A',
                    'status' => 'Active',
                ],
            ])->assertOk();

        $request = InventoryRequest::firstOrFail();
        $this->assertSame('Authenticated Employee', $request->requestor);
    }

    public function test_demo_seeder_is_additive_and_hashes_required_password(): void
    {
        $existingUser = User::factory()->create();

        $this->seed(DemoUserSeeder::class);

        $demoUser = User::where('email', 'employee@gmail.com')->firstOrFail();

        $this->assertSame('Demo Employee', $demoUser->name);
        $this->assertTrue(Hash::check('admin123', $demoUser->password));
        $this->assertDatabaseHas('users', ['id' => $existingUser->id]);
    }

    public function test_registration_is_not_available(): void
    {
        $this->get('/register')->assertNotFound();
    }
}
