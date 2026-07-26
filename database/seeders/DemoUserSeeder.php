<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'employee@gmail.com'],
            [
                'name' => 'Demo Employee',
                'password' => Hash::make('admin123'),
            ],
        );
    }
}
