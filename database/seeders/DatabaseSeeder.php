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
        $this->call([
            // // 1. Master Items must be seeded first to establish foreign keys
            ItemSeeder::class,
            InitialAdminUserSeeder::class,

            // // // 2. Dependent seeders run sequentially after
            // // // (Ensure these files have their references updated to item_id if they exist)
            // InventorySystemSeeder::class,
            // // InventoryStockMovementSeeder::class,
        ]);
    }
}
