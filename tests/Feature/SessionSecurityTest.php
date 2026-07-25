<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_json_requests_receive_unauthorized_instead_of_login_html_after_expiry(): void
    {
        $this->getJson('/inventory/api/state')
            ->assertUnauthorized()
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_invalid_csrf_token_returns_419_for_an_authenticated_mutation(): void
    {
        $this->app->detectEnvironment(fn () => 'local');

        $this->withMiddleware(ValidateCsrfToken::class)
            ->actingAs(User::factory()->create())
            ->postJson('/api/requests', [
                'type' => 'ADD',
                'proposed_data' => ['name' => 'Blocked by CSRF'],
            ])
            ->assertStatus(419);
    }

    public function test_logout_prevents_page_and_ajax_access_from_the_same_session(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post('/logout')->assertRedirect('/login');

        $this->get('/')->assertRedirect('/login');
        $this->postJson('/inventory/api/bundle')->assertUnauthorized();
    }
}
