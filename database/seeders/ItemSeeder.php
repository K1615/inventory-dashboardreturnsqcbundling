<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'id' => 'PART-001',
                'name' => 'RTX 4090 GPU',
                'category' => 'GPU',
                'desc' => 'High-end graphics card.',
                'status' => 'Active',
                'qty' => 15,
                'price' => 1600.00,
                'warehouse' => 'East Hub',
                'zone' => 'Zone C',
                'minLimit' => 5,
                'maxLimit' => 30,
                'auto_reorder' => false,
                'reorder_qty' => null,
            ]
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}