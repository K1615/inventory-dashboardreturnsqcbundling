<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Part;
use App\Models\StockMovement;

class InventoryStockMovementSeeder extends Seeder
{
    public function run(): void
    {
        $parts = [
            ['id' => 'PART-001', 'name' => 'Intel Core i7-14700K', 'category' => 'CPU', 'stock' => 24, 'exp_date' => '2029-12-15'],
            ['id' => 'PART-002', 'name' => 'AMD Ryzen 7 7800X3D', 'category' => 'CPU', 'stock' => 18, 'exp_date' => '2029-08-22'],
            ['id' => 'PART-003', 'name' => 'NVIDIA RTX 4070 Super', 'category' => 'GPU', 'stock' => 12, 'exp_date' => '2029-03-10'],
            ['id' => 'PART-004', 'name' => 'ASUS ROG Strix B650-A', 'category' => 'Motherboard', 'stock' => 15, 'exp_date' => '2028-11-05'],
            ['id' => 'PART-005', 'name' => 'Corsair Vengeance 32GB DDR5', 'category' => 'RAM', 'stock' => 40, 'exp_date' => '2031-01-01'],
            ['id' => 'PART-006', 'name' => 'Samsung 990 Pro NVMe 2TB', 'category' => 'Storage', 'stock' => 35, 'exp_date' => '2031-05-19'],
            ['id' => 'PART-007', 'name' => 'Corsair RM850x 850W Gold', 'category' => 'PSU', 'stock' => 20, 'exp_date' => '2036-02-14'],
            ['id' => 'PART-008', 'name' => 'MSI Ventus 3X RTX 4060 Ti', 'category' => 'GPU', 'stock' => 8, 'exp_date' => '2029-06-30'],
            ['id' => 'PART-009', 'name' => 'Crucial P3 Plus 1TB SSD', 'category' => 'Storage', 'stock' => 50, 'exp_date' => '2029-09-12'],
            ['id' => 'PART-010', 'name' => 'G.Skill Trident Z5 64GB', 'category' => 'RAM', 'stock' => 14, 'exp_date' => '2031-07-08']
        ];

        foreach ($parts as $part) {
            Part::create($part);
        }

        $movements = [
            ['date' => '2026-07-01', 'tx_id' => 'TX-10395', 'part_id' => 'PART-001', 'type' => 'Stock-In', 'qty' => 15, 'note' => 'Initial bulk order stock arrival', 'status' => 'Approved', 'user' => 'Admin 1'],
            ['date' => '2026-07-02', 'tx_id' => 'TX-10396', 'part_id' => 'PART-002', 'type' => 'Stock-In', 'qty' => 10, 'note' => 'Factory batch arrival', 'status' => 'Approved', 'user' => 'Admin 2'],
            ['date' => '2026-07-03', 'tx_id' => 'TX-10397', 'part_id' => 'PART-004', 'type' => 'Stock-Out', 'qty' => 3, 'note' => 'B2B Client Fulfillment', 'status' => 'Approved', 'user' => 'Admin 1'],
            ['date' => '2026-07-04', 'tx_id' => 'TX-10398', 'part_id' => 'PART-009', 'type' => 'Warehouse Transfer', 'qty' => 10, 'note' => '[Main Warehouse A ➔ Branch Location B] Seasonal transfer', 'status' => 'Approved', 'user' => 'Admin 3'],
            ['date' => '2026-07-05', 'tx_id' => 'TX-10400', 'part_id' => 'PART-010', 'type' => 'Product Return', 'qty' => 2, 'note' => 'Customer switch request return', 'status' => 'Approved', 'user' => 'Admin 2'],
            ['date' => '2026-07-06', 'tx_id' => 'TX-10401', 'part_id' => 'PART-001', 'type' => 'Stock-In', 'qty' => 10, 'note' => 'Supplier stock arrival', 'status' => 'Approved', 'user' => 'Admin 1'],
            ['date' => '2026-07-08', 'tx_id' => 'TX-10402', 'part_id' => 'PART-003', 'type' => 'Stock-Out', 'qty' => 2, 'note' => 'Customer store purchase', 'status' => 'Approved', 'user' => 'Admin 2'],
            ['date' => '2026-07-09', 'tx_id' => 'TX-10403', 'part_id' => 'PART-006', 'type' => 'Warehouse Transfer', 'qty' => 5, 'note' => '[Main Warehouse A ➔ Branch Location B] Regular transfer relocation', 'status' => 'Approved', 'user' => 'Admin 1'],
            ['date' => '2026-07-10', 'tx_id' => 'TX-10404', 'part_id' => 'PART-002', 'type' => 'Product Return', 'qty' => 1, 'note' => 'Box damaged during transport, return for checklist inspection', 'status' => 'Pending', 'user' => 'Admin 3'],
            ['date' => '2026-07-11', 'tx_id' => 'TX-10405', 'part_id' => 'PART-005', 'type' => 'Stock-In', 'qty' => 20, 'note' => 'Inbound wholesale shipment arrival', 'status' => 'Pending', 'user' => 'Admin 1'],
            ['date' => '2026-07-12', 'tx_id' => 'TX-10406', 'part_id' => 'PART-007', 'type' => 'Stock-Out', 'qty' => 4, 'note' => 'Canceled customer preorder list item', 'status' => 'Voided', 'user' => 'Admin 2']
        ];

        foreach ($movements as $mv) {
            StockMovement::create($mv);
        }
    }
}