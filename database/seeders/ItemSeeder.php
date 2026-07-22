<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $items = [
            // ================== PROCESSORS ==================
            [
                'id' => 'P001', 'name' => 'Intel Core i9-14900K', 'category' => 'Processor', 
                'qty' => 50, 'price' => 589.99, 'warehouse' => 'Warehouse A', 'zone' => 'Zone A',
                'minLimit' => 5, 'maxLimit' => 20, 'auto_reorder' => true, 'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 'P002', 'name' => 'AMD Ryzen 9 7950X', 'category' => 'Processor', 
                'qty' => 50, 'price' => 549.99, 'warehouse' => 'Warehouse B', 'zone' => 'Zone B',
                'minLimit' => 5, 'maxLimit' => 20, 'auto_reorder' => true, 'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 'P016', 'name' => 'Intel Core i5-13400F', 'category' => 'Processor', 
                'qty' => 50, 'price' => 185.00, 'warehouse' => 'Warehouse C', 'zone' => 'Zone C',
                'minLimit' => 5, 'maxLimit' => 30, 'auto_reorder' => true, 'created_at' => $now, 'updated_at' => $now
            ],

            // ================== GRAPHICS CARDS ==================
            [
                'id' => 'P003', 'name' => 'NVIDIA GeForce RTX 4090', 'category' => 'Graphics Card', 
                'qty' => 50, 'price' => 1599.99, 'warehouse' => 'Warehouse A', 'zone' => 'Zone A',
                'minLimit' => 2, 'maxLimit' => 50, 'auto_reorder' => true, 'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 'P004', 'name' => 'AMD Radeon RX 7900 XTX', 'category' => 'Graphics Card', 
                'qty' => 50, 'price' => 999.99, 'warehouse' => 'Warehouse B', 'zone' => 'Zone B',
                'minLimit' => 2, 'maxLimit' => 50, 'auto_reorder' => true, 'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 'P017', 'name' => 'MSI RTX 4060 Ventus 2X', 'category' => 'Graphics Card', 
                'qty' => 50, 'price' => 299.99, 'warehouse' => 'Warehouse C', 'zone' => 'Zone C',
                'minLimit' => 5, 'maxLimit' => 25, 'auto_reorder' => true, 'created_at' => $now, 'updated_at' => $now
            ],

            // ================== MOTHERBOARDS ==================
            [
                'id' => 'P009', 'name' => 'ASUS ROG Maximus Z790', 'category' => 'Motherboard', 
                'qty' => 50, 'price' => 629.99, 'warehouse' => 'Warehouse A', 'zone' => 'Zone A',
                'minLimit' => 5, 'maxLimit' => 15, 'auto_reorder' => true, 'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 'P010', 'name' => 'MSI MAG B650 Tomahawk', 'category' => 'Motherboard', 
                'qty' => 50, 'price' => 219.99, 'warehouse' => 'Warehouse B', 'zone' => 'Zone B',
                'minLimit' => 5, 'maxLimit' => 15, 'auto_reorder' => true, 'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 'P018', 'name' => 'Gigabyte B760M DS3H', 'category' => 'Motherboard', 
                'qty' => 50, 'price' => 509.99, 'warehouse' => 'Warehouse C', 'zone' => 'Zone C',
                'minLimit' => 5, 'maxLimit' => 30, 'auto_reorder' => true, 'created_at' => $now, 'updated_at' => $now
            ],

            // ================== MEMORY (RAM) ==================
            [
                'id' => 'P005', 'name' => 'Corsair Vengeance 32GB DDR5', 'category' => 'Memory', 
                'qty' => 50, 'price' => 114.99, 'warehouse' => 'Warehouse A', 'zone' => 'Zone A',
                'minLimit' => 50, 'maxLimit' => 50, 'auto_reorder' => true, 'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 'P006', 'name' => 'G.Skill Trident Z5 64GB DDR5', 'category' => 'Memory', 
                'qty' => 50, 'price' => 209.99, 'warehouse' => 'Warehouse B', 'zone' => 'Zone B',
                'minLimit' => 50, 'maxLimit' => 50, 'auto_reorder' => true, 'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 'P019', 'name' => 'Kingston FURY Beast 16GB DDR4', 'category' => 'Memory', 
                'qty' => 50, 'price' => 45.00, 'warehouse' => 'Warehouse C', 'zone' => 'Zone C',
                'minLimit' => 50, 'maxLimit' => 60, 'auto_reorder' => true, 'created_at' => $now, 'updated_at' => $now
            ],

            // ================== STORAGE ==================
            [
                'id' => 'P007', 'name' => 'Samsung 990 PRO 2TB NVMe', 'category' => 'Storage', 
                'qty' => 50, 'price' => 189.99, 'warehouse' => 'Warehouse A', 'zone' => 'Zone A',
                'minLimit' => 50, 'maxLimit' => 40, 'auto_reorder' => true, 'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 'P008', 'name' => 'WD Black SN850X 1TB NVMe', 'category' => 'Storage', 
                'qty' => 50, 'price' => 89.99, 'warehouse' => 'Warehouse B', 'zone' => 'Zone B',
                'minLimit' => 50, 'maxLimit' => 40, 'auto_reorder' => true, 'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 'P020', 'name' => 'Crucial P3 500GB NVMe', 'category' => 'Storage', 
                'qty' => 50, 'price' => 35.99, 'warehouse' => 'Warehouse C', 'zone' => 'Zone C',
                'minLimit' => 50, 'maxLimit' => 60, 'auto_reorder' => true, 'created_at' => $now, 'updated_at' => $now
            ],

            // ================== POWER SUPPLY ==================
            [
                'id' => 'P011', 'name' => 'Corsair RM5000x 5000W', 'category' => 'Power Supply', 
                'qty' => 50, 'price' => 169.99, 'warehouse' => 'Warehouse A', 'zone' => 'Zone A',
                'minLimit' => 5, 'maxLimit' => 20, 'auto_reorder' => true, 'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 'P012', 'name' => 'EVGA SuperNOVA 850W', 'category' => 'Power Supply', 
                'qty' => 50, 'price' => 129.99, 'warehouse' => 'Warehouse B', 'zone' => 'Zone B',
                'minLimit' => 5, 'maxLimit' => 20, 'auto_reorder' => true, 'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 'P021', 'name' => 'Thermaltake Smart 500W', 'category' => 'Power Supply', 
                'qty' => 50, 'price' => 39.99, 'warehouse' => 'Warehouse C', 'zone' => 'Zone C',
                'minLimit' => 5, 'maxLimit' => 30, 'auto_reorder' => true, 'created_at' => $now, 'updated_at' => $now
            ],

            // ================== CASE ==================
            [
                'id' => 'P013', 'name' => 'NZXT H7 Flow', 'category' => 'Case', 
                'qty' => 50, 'price' => 129.99, 'warehouse' => 'Warehouse A', 'zone' => 'Zone A',
                'minLimit' => 5, 'maxLimit' => 15, 'auto_reorder' => true, 'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 'P014', 'name' => 'Fractal Design North', 'category' => 'Case', 
                'qty' => 50, 'price' => 139.99, 'warehouse' => 'Warehouse B', 'zone' => 'Zone B',
                'minLimit' => 5, 'maxLimit' => 15, 'auto_reorder' => true, 'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 'P022', 'name' => 'Montech AIR 903 Base', 'category' => 'Case', 
                'qty' => 50, 'price' => 65.00, 'warehouse' => 'Warehouse C', 'zone' => 'Zone C',
                'minLimit' => 5, 'maxLimit' => 25, 'auto_reorder' => true, 'created_at' => $now, 'updated_at' => $now
            ],

            // ================== COOLER ==================
            [
                'id' => 'P015', 'name' => 'Noctua NH-D15', 'category' => 'Cooler', 
                'qty' => 50, 'price' => 119.99, 'warehouse' => 'Warehouse A', 'zone' => 'Zone A',
                'minLimit' => 5, 'maxLimit' => 20, 'auto_reorder' => true, 'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 'P023', 'name' => 'Corsair iCUE H150i AIO', 'category' => 'Cooler', 
                'qty' => 50, 'price' => 189.99, 'warehouse' => 'Warehouse B', 'zone' => 'Zone B',
                'minLimit' => 5, 'maxLimit' => 20, 'auto_reorder' => true, 'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 'P024', 'name' => 'DeepCool AK400', 'category' => 'Cooler', 
                'qty' => 50, 'price' => 34.99, 'warehouse' => 'Warehouse C', 'zone' => 'Zone C',
                'minLimit' => 5, 'maxLimit' => 30, 'auto_reorder' => true, 'created_at' => $now, 'updated_at' => $now
            ]
        ];

        DB::table('items')->insert($items);
    }
}