<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use RuntimeException;

class InitialAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $credentials = [
            'name' => trim((string) config('initial-admin.name')),
            'email' => mb_strtolower(trim((string) config('initial-admin.email'))),
            'password' => (string) config('initial-admin.password'),
        ];

        if (in_array('', $credentials, true)) {
            if (app()->environment('production')) {
                throw new RuntimeException(
                    'INITIAL_ADMIN_NAME, INITIAL_ADMIN_EMAIL, and INITIAL_ADMIN_PASSWORD are required in production.',
                );
            }

            $this->command?->warn(
                'Initial user not created: set INITIAL_ADMIN_NAME, INITIAL_ADMIN_EMAIL, and INITIAL_ADMIN_PASSWORD.',
            );

            return;
        }

        $validated = Validator::make($credentials, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => [
                'required',
                'string',
                Password::min(12)->mixedCase()->letters()->numbers()->symbols(),
            ],
        ])->validate();

        $user = User::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'password' => Hash::make($validated['password']),
            ],
        );

        $this->command?->info(
            $user->wasRecentlyCreated
                ? 'Initial user created.'
                : 'Initial user already exists; the existing name and password were preserved.',
        );
    }
}
