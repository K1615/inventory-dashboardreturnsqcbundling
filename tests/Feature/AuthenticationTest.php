<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private const PASSWORD = 'TestPassword!123';

    public function test_login_page_is_available_to_guests(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Sign in')
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false)
            ->assertSee('name="remember"', false);
    }

    public function test_valid_credentials_authenticate_and_regenerate_the_session(): void
    {
        $user = $this->createUser();

        $this->withSession(['session_marker' => 'before-login']);
        $oldSessionId = session()->getId();

        $response = $this->post('/login', [
            'email' => strtoupper($user->email),
            'password' => self::PASSWORD,
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
        $this->assertNotSame($oldSessionId, session()->getId());
    }

    public function test_login_redirects_to_the_originally_intended_page(): void
    {
        $user = $this->createUser();

        $this->get('/warehouse')->assertRedirect('/login');

        $this->post('/login', [
            'email' => $user->email,
            'password' => self::PASSWORD,
        ])->assertRedirect('/warehouse');
    }

    public function test_invalid_credentials_use_a_generic_error_and_do_not_authenticate(): void
    {
        $user = $this->createUser();

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'IncorrectPassword!123',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'email' => trans('auth.failed'),
        ]);
        $response->assertSessionHasInput('email', $user->email);
        $response->assertSessionMissing('_old_input.password');
        $this->assertGuest();
    }

    public function test_login_validation_rejects_missing_or_invalid_fields(): void
    {
        $this->post('/login', [])
            ->assertSessionHasErrors(['email', 'password']);

        $this->post('/login', [
            'email' => 'not-an-email',
            'password' => self::PASSWORD,
            'remember' => 'not-a-boolean',
        ])->assertSessionHasErrors(['email', 'remember']);
    }

    public function test_remember_me_is_accepted_and_creates_a_remember_token(): void
    {
        $user = $this->createUser();

        $this->post('/login', [
            'email' => $user->email,
            'password' => self::PASSWORD,
            'remember' => true,
        ])->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->remember_token);
    }

    public function test_authenticated_users_are_redirected_away_from_login(): void
    {
        $this->actingAs($this->createUser())
            ->get('/login')
            ->assertRedirect('/');
    }

    public function test_repeated_failed_logins_are_rate_limited(): void
    {
        $email = 'limited@example.test';
        $key = mb_strtolower($email).'|127.0.0.1';
        RateLimiter::clear($key);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/login', [
                'email' => $email,
                'password' => 'IncorrectPassword!123',
            ])->assertSessionHasErrors('email');
        }

        $response = $this->post('/login', [
            'email' => $email,
            'password' => 'IncorrectPassword!123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString(
            'Too many login attempts',
            $response->getSession()->get('errors')->first('email'),
        );
    }

    public function test_logout_invalidates_the_session_and_redirects_to_login(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)->withSession(['private_marker' => 'remove-me']);
        $oldSessionId = session()->getId();

        $this->post('/logout')
            ->assertRedirect('/login')
            ->assertSessionMissing('private_marker');

        $this->assertGuest();
        $this->assertNotSame($oldSessionId, session()->getId());
    }

    public function test_get_logout_is_unavailable_and_a_repeated_post_is_safe(): void
    {
        $this->get('/logout')->assertMethodNotAllowed();
        $this->post('/logout')->assertRedirect('/login');
    }

    private function createUser(): User
    {
        return User::factory()->create([
            'name' => 'Authenticated Operator',
            'email' => 'operator@example.test',
            'password' => Hash::make(self::PASSWORD),
        ]);
    }
}
