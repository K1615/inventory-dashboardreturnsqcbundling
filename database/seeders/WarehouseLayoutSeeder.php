<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WarehouseInventoryItem;
use App\Models\StockMovementRequest;
use App\Models\AuditMovementLog;
use Carbon\Carbon;

class WarehouseLayoutSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Inventory Items
        $items = [
            ['type' => 'CPU', 'name' => 'Intel Core i7-13700K Processor', 'warehouse' => 'Main Warehouse', 'zone' => 'Zone A', 'qty' => 45],
            ['type' => 'CPU', 'name' => 'AMD Ryzen 7 7800X3D CPU', 'warehouse' => 'North Branch', 'zone' => 'Zone A', 'qty' => 28],
            ['type' => 'GPU', 'name' => 'NVIDIA RTX 4070 Super 12GB', 'warehouse' => 'Main Warehouse', 'zone' => 'Zone B', 'qty' => 15],
            ['type' => 'GPU', 'name' => 'ASUS ROG Strix RTX 4080', 'warehouse' => 'East Hub', 'zone' => 'Zone B', 'qty' => 8],
            ['type' => 'Storage', 'name' => 'Samsung 990 Pro 2TB NVMe SSD', 'warehouse' => 'Main Warehouse', 'zone' => 'Zone C', 'qty' => 120],
            ['type' => 'Storage', 'name' => 'Crucial P3 Plus 1TB M.2 SSD', 'warehouse' => 'North Branch', 'zone' => 'Zone C', 'qty' => 85],
            ['type' => 'RAM', 'name' => 'Corsair Vengeance DDR5 32GB RAM', 'warehouse' => 'East Hub', 'zone' => 'Zone D', 'qty' => 60],
            ['type' => 'RAM', 'name' => 'G.Skill Trident Z5 Neo 64GB RAM', 'warehouse' => 'Main Warehouse', 'zone' => 'Zone D', 'qty' => 22],
            // Extra items to achieve required realistic scope
            ['type' => 'CPU', 'name' => 'Intel Core i9-14900K Processor', 'warehouse' => 'East Hub', 'zone' => 'Zone A', 'qty' => 12],
            ['type' => 'GPU', 'name' => 'AMD Radeon RX 7900 XTX', 'warehouse' => 'North Branch', 'zone' => 'Zone B', 'qty' => 19],
            ['type' => 'Storage', 'name' => 'WD Black SN850X 2TB NVMe', 'warehouse' => 'East Hub', 'zone' => 'Zone C', 'qty' => 70],
            ['type' => 'RAM', 'name' => 'Kingston FURY Beast 16GB DDR4', 'warehouse' => 'Main Warehouse', 'zone' => 'Zone E', 'qty' => 140],
            ['type' => 'CPU', 'name' => 'AMD Ryzen 5 5600X CPU', 'warehouse' => 'Main Warehouse', 'zone' => 'Zone A', 'qty' => 35],
            ['type' => 'GPU', 'name' => 'MSI Ventus RTX 4060 Ti', 'warehouse' => 'East Hub', 'zone' => 'Zone B', 'qty' => 25],
            ['type' => 'Storage' ,'name' => 'Seagate IronWolf 4TB NAS HDD', 'warehouse' => 'North Branch', 'zone' => 'Zone C', 'qty' => 40]
        ];

        foreach ($items as $item) {
            WarehouseInventoryItem::create(array_merge($item, [
                'last_moved' => Carbon::now()->subDays(rand(1, 5))->setHour(rand(8, 17))->setMinute(rand(10, 59))
            ]));
        }

        // 2. Seed Pending Requests
        $requests = [
            ['warehouse_inventory_item_id' => 1, 'requester' => 'Admin 1', 'name' => 'Intel Core i7-13700K Processor', 'from_wh' => 'Main Warehouse', 'from_zone' => 'Zone A', 'to_wh' => 'East Hub', 'to_zone' => 'Zone A', 'qty' => 10, 'planned_date' => '2026-07-13'],
            ['warehouse_inventory_item_id' => 3, 'requester' => 'Admin 2', 'name' => 'NVIDIA RTX 4070 Super 12GB', 'from_wh' => 'Main Warehouse', 'from_zone' => 'Zone B', 'to_wh' => 'North Branch', 'to_zone' => 'Zone B', 'qty' => 5, 'planned_date' => '2026-07-14'],
            ['warehouse_inventory_item_id' => 5, 'requester' => 'Admin 3', 'name' => 'Samsung 990 Pro 2TB NVMe SSD', 'from_wh' => 'Main Warehouse', 'from_zone' => 'Zone C', 'to_wh' => 'East Hub', 'to_zone' => 'Zone C', 'qty' => 40, 'planned_date' => '2026-07-14']
        ];
        foreach ($requests as $req) {
            StockMovementRequest::create($req);
        }

        // 3. Seed Audit History Logs
        $logs = [
            ['type' => 'APPROVED', 'name' => 'G.Skill Trident Z5 Neo 64GB RAM', 'from_wh' => 'Main Warehouse', 'to_wh' => 'North Branch', 'zone' => 'Zone D', 'qty' => 8, 'raw_date' => '2026-07-12'],
            ['type' => 'VOIDED', 'name' => 'ASUS ROG Strix RTX 4080', 'from_wh' => 'East Hub', 'to_wh' => 'Main Warehouse', 'zone' => 'Zone B', 'qty' => 2, 'raw_date' => '2026-07-11']
        ];
        foreach ($logs as $log) {
            AuditMovementLog::create($log);
        }
    }
}