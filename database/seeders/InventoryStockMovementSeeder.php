<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\StockMovement;

class InventoryStockMovementSeeder extends Seeder
{
    public function run(): void
    {
        $parts = [
            ['id' => 'PART-001', 'name' => 'Intel Core i7-14700K', 'category' => 'CPU', 'qty' => 24, 'warehouse' => 'Main Warehouse'],
            ['id' => 'PART-002', 'name' => 'AMD Ryzen 7 7800X3D', 'category' => 'CPU', 'qty' => 18, 'warehouse' => 'Main Warehouse'],
            ['id' => 'PART-003', 'name' => 'NVIDIA RTX 4070 Super', 'category' => 'GPU', 'qty' => 12, 'warehouse' => 'Main Warehouse'],
            ['id' => 'PART-004', 'name' => 'ASUS ROG Strix B650-A', 'category' => 'Motherboard', 'qty' => 15, 'warehouse' => 'Main Warehouse'],
            ['id' => 'PART-005', 'name' => 'Corsair Vengeance 32GB DDR5', 'category' => 'RAM', 'qty' => 40, 'warehouse' => 'Main Warehouse'],
            ['id' => 'PART-006', 'name' => 'Samsung 990 Pro NVMe 2TB', 'category' => 'Storage', 'qty' => 35, 'warehouse' => 'Main Warehouse'],
            ['id' => 'PART-007', 'name' => 'Corsair RM850x 850W Gold', 'category' => 'PSU', 'qty' => 20, 'warehouse' => 'Main Warehouse'],
            ['id' => 'PART-008', 'name' => 'MSI Ventus 3X RTX 4060 Ti', 'category' => 'GPU', 'qty' => 8, 'warehouse' => 'Main Warehouse'],
            ['id' => 'PART-009', 'name' => 'Crucial P3 Plus 1TB SSD', 'category' => 'Storage', 'qty' => 50, 'warehouse' => 'Main Warehouse'],
            ['id' => 'PART-010', 'name' => 'G.Skill Trident Z5 64GB', 'category' => 'RAM', 'qty' => 14, 'warehouse' => 'Main Warehouse']
        ];

        foreach ($parts as $part) {
            // This is the fix to stop the duplication crash
            Item::updateOrCreate(
                ['id' => $part['id']],
                $part
            );
        }

        $movements = [
            ['date' => '2026-07-01', 'tx_id' => 'TX-10395', 'item_id' => 'PART-001', 'type' => 'Stock-In', 'qty' => 15, 'note' => 'Initial bulk order stock arrival', 'status' => 'Approved', 'user' => 'Admin 1'],
            ['date' => '2026-07-02', 'tx_id' => 'TX-10396', 'item_id' => 'PART-002', 'type' => 'Stock-In', 'qty' => 10, 'note' => 'Factory batch arrival', 'status' => 'Approved', 'user' => 'Admin 2'],
            ['date' => '2026-07-03', 'tx_id' => 'TX-10397', 'item_id' => 'PART-004', 'type' => 'Stock-Out', 'qty' => 3, 'note' => 'B2B Client Fulfillment', 'status' => 'Approved', 'user' => 'Admin 1'],
            ['date' => '2026-07-04', 'tx_id' => 'TX-10398', 'item_id' => 'PART-009', 'type' => 'Warehouse Transfer', 'qty' => 10, 'note' => '[Main Warehouse A ➔ Branch Location B] Seasonal transfer', 'status' => 'Approved', 'user' => 'Admin 3'],
            ['date' => '2026-07-05', 'tx_id' => 'TX-10400', 'item_id' => 'PART-010', 'type' => 'Product Return', 'qty' => 2, 'note' => 'Customer switch request return', 'status' => 'Approved', 'user' => 'Admin 2'],
            ['date' => '2026-07-06', 'tx_id' => 'TX-10401', 'item_id' => 'PART-001', 'type' => 'Stock-In', 'qty' => 10, 'note' => 'Supplier stock arrival', 'status' => 'Approved', 'user' => 'Admin 1'],
            ['date' => '2026-07-08', 'tx_id' => 'TX-10402', 'item_id' => 'PART-003', 'type' => 'Stock-Out', 'qty' => 2, 'note' => 'Customer store purchase', 'status' => 'Approved', 'user' => 'Admin 2'],
            ['date' => '2026-07-09', 'tx_id' => 'TX-10403', 'item_id' => 'PART-006', 'type' => 'Warehouse Transfer', 'qty' => 5, 'note' => '[Main Warehouse A ➔ Branch Location B] Regular transfer relocation', 'status' => 'Approved', 'user' => 'Admin 1'],
            ['date' => '2026-07-10', 'tx_id' => 'TX-10404', 'item_id' => 'PART-002', 'type' => 'Product Return', 'qty' => 1, 'note' => 'Box damaged during transport, return for checklist inspection', 'status' => 'Pending', 'user' => 'Admin 3'],
            ['date' => '2026-07-11', 'tx_id' => 'TX-10405', 'item_id' => 'PART-005', 'type' => 'Stock-In', 'qty' => 20, 'note' => 'Inbound wholesale shipment arrival', 'status' => 'Pending', 'user' => 'Admin 1'],
            ['date' => '2026-07-12', 'tx_id' => 'TX-10406', 'item_id' => 'PART-007', 'type' => 'Stock-Out', 'qty' => 4, 'note' => 'Canceled customer preorder list item', 'status' => 'Voided', 'user' => 'Admin 2']
        ];

        foreach ($movements as $mv) {
            StockMovement::create($mv);
        }
    }
}