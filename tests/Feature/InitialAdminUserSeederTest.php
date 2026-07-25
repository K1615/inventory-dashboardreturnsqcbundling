<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\InitialAdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InitialAdminUserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_initial_user_seeder_is_repeatable_and_preserves_existing_credentials(): void
    {
        config()->set('initial-admin', [
            'name' => 'Initial Administrator',
            'email' => 'Initial.Admin@Example.Test',
            'password' => 'FirstSecure!123',
        ]);

        $this->seed(InitialAdminUserSeeder::class);

        $user = User::sole();
        $originalHash = $user->password;

        config()->set('initial-admin.name', 'Replacement Name');
        config()->set('initial-admin.password', 'SecondSecure!456');

        $this->seed(InitialAdminUserSeeder::class);

        $this->assertSame(1, User::count());
        $this->assertSame('initial.admin@example.test', $user->fresh()->email);
        $this->assertSame('Initial Administrator', $user->fresh()->name);
        $this->assertSame($originalHash, $user->fresh()->password);
        $this->assertTrue(Hash::check('FirstSecure!123', $user->fresh()->password));
        $this->assertFalse(Hash::check('SecondSecure!456', $user->fresh()->password));
    }
}
