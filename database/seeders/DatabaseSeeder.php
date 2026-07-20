<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call the specific submodule seeder we created earlier
        $this->call([
            InventorySystemSeeder::class,
             ItemSeeder::class
        ]);
    }
}
