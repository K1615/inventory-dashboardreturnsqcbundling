<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InventoryItem;
use App\Models\SystemLog;
use App\Models\QcInspection;
use App\Models\RmaRequest;
use App\Models\ReturnsAuditLog;

class InventorySystemSeeder extends Seeder
{
    public function run(): void
    {
        // ALL 41 Original Inventory Items verbatim
        $items = [
            ['id' => 'PC-001', 'name' => 'Intel Core i9-14900K Processor Box', 'category' => 'CPU', 'stock' => 14, 'price' => 529.99, 'warehouse' => 'Warehouse A'],
            ['id' => 'PC-002', 'name' => 'AMD Ryzen 7 7800X3D Processor', 'category' => 'CPU', 'stock' => 4, 'price' => 369.00, 'warehouse' => 'Warehouse B'],
            ['id' => 'PC-003', 'name' => 'NVIDIA GeForce RTX 4090 24GB Card', 'category' => 'GPU', 'stock' => 0, 'price' => 1599.99, 'warehouse' => 'Warehouse A'],
            ['id' => 'PC-004', 'name' => 'ASUS ROG Strix RTX 4070 Ti Super', 'category' => 'GPU', 'stock' => 12, 'price' => 849.50, 'warehouse' => 'Warehouse C'],
            ['id' => 'PC-005', 'name' => 'Corsair Vengeance DDR5 32GB Kit', 'category' => 'RAM', 'stock' => 25, 'price' => 114.99, 'warehouse' => 'Warehouse B'],
            ['id' => 'PC-006', 'name' => 'G.Skill Trident Z5 RGB 64GB RAM', 'category' => 'RAM', 'stock' => 3, 'price' => 219.99, 'warehouse' => 'Warehouse A'],
            ['id' => 'PC-007', 'name' => 'Samsung 990 Pro 2TB NVMe M.2 SSD', 'category' => 'Storage', 'stock' => 40, 'price' => 169.99, 'warehouse' => 'Warehouse A'],
            ['id' => 'PC-008', 'name' => 'Crucial T700 1TB PCIe 5.0 SSD', 'category' => 'Storage', 'stock' => 18, 'price' => 149.50, 'warehouse' => 'Warehouse C'],
            ['id' => 'PC-009', 'name' => 'Corsair RM1000x 1000W Power Supply', 'category' => 'Power Supply', 'stock' => 15, 'price' => 189.99, 'warehouse' => 'Warehouse B'],
            ['id' => 'PC-010', 'name' => 'Seasonic Vertex GX-850 Gold PSU', 'category' => 'Power Supply', 'stock' => 0, 'price' => 159.99, 'warehouse' => 'Warehouse A'],
            ['id' => 'PC-011', 'name' => 'Intel Core i5-14600K Processor', 'category' => 'CPU', 'stock' => 22, 'price' => 299.00, 'warehouse' => 'Warehouse C'],
            ['id' => 'PC-012', 'name' => 'MSI Ventus 3X RTX 4060 Ti 8GB', 'category' => 'GPU', 'stock' => 19, 'price' => 389.99, 'warehouse' => 'Warehouse B'],
            ['id' => 'PC-013', 'name' => 'Kingston FURY Beast 16GB DDR5 RAM', 'category' => 'RAM', 'stock' => 30, 'price' => 59.99, 'warehouse' => 'Warehouse A'],
            ['id' => 'PC-014', 'name' => 'WD Black SN850X 4TB NVMe SSD', 'category' => 'Storage', 'stock' => 7, 'price' => 309.00, 'warehouse' => 'Warehouse B'],
            ['id' => 'PC-015', 'name' => 'EVGA SuperNOVA 750 GT Power Supply', 'category' => 'Power Supply', 'stock' => 5, 'price' => 124.50, 'warehouse' => 'Warehouse C'],
            ['id' => 'PRD-001', 'name' => 'Intel Core i7-13700K', 'category' => 'CPU', 'stock' => 5, 'price' => 400.00, 'warehouse' => 'Warehouse A'],
            ['id' => 'PRD-002', 'name' => 'Corsair Vengeance 32GB RAM', 'category' => 'RAM', 'stock' => 8, 'price' => 100.00, 'warehouse' => 'Warehouse B'],
            ['id' => 'PRD-003', 'name' => 'Samsung 990 Pro 2TB SSD', 'category' => 'Storage', 'stock' => 3, 'price' => 180.00, 'warehouse' => 'Warehouse A'],
            ['id' => 'PRD-004', 'name' => 'ASUS ROG Strix B650E-F', 'category' => 'Motherboard', 'stock' => 4, 'price' => 250.00, 'warehouse' => 'Warehouse C'],
            ['id' => 'PRD-005', 'name' => 'EVGA SuperNOVA 850W', 'category' => 'Power Supply', 'stock' => 2, 'price' => 120.00, 'warehouse' => 'Warehouse B'],
            ['id' => 'PRD-006', 'name' => 'NZXT Kraken X63 AIO', 'category' => 'Cooler', 'stock' => 7, 'price' => 150.00, 'warehouse' => 'Warehouse A'],
            // THESE ARE THE IDS THE BUNDLING PRESETS LOOK FOR:
            ['id' => 'P001', 'category' => 'CPU', 'name' => 'Intel Core i7-13700K', 'stock' => 12, 'price' => 389.99, 'warehouse' => 'Warehouse A'],
            ['id' => 'P002', 'category' => 'CPU', 'name' => 'AMD Ryzen 7 7800X3D', 'stock' => 8, 'price' => 359.00, 'warehouse' => 'Warehouse B'],
            ['id' => 'P003', 'category' => 'GPU', 'name' => 'NVIDIA RTX 4070 Ti', 'stock' => 5, 'price' => 799.99, 'warehouse' => 'Warehouse C'],
            ['id' => 'P004', 'category' => 'GPU', 'name' => 'AMD Radeon RX 7800 XT', 'stock' => 7, 'price' => 499.00, 'warehouse' => 'Warehouse A'],
            ['id' => 'P005', 'category' => 'RAM', 'name' => 'Corsair Vengeance 32GB DDR5', 'stock' => 20, 'price' => 109.99, 'warehouse' => 'Warehouse B'],
            ['id' => 'P006', 'category' => 'RAM', 'name' => 'G.Skill Trident Z5 32GB DDR5', 'stock' => 15, 'price' => 129.99, 'warehouse' => 'Warehouse C'],
            ['id' => 'P007', 'category' => 'Storage', 'name' => 'Samsung 990 Pro 2TB SSD', 'stock' => 18, 'price' => 179.99, 'warehouse' => 'Warehouse A'],
            ['id' => 'P008', 'category' => 'Storage', 'name' => 'WD Black SN850X 1TB SSD', 'stock' => 25, 'price' => 99.99, 'warehouse' => 'Warehouse B'],
            ['id' => 'P009', 'category' => 'Motherboard', 'name' => 'ASUS ROG Strix Z790-E', 'stock' => 6, 'price' => 429.99, 'warehouse' => 'Warehouse C'],
            ['id' => 'P010', 'category' => 'Motherboard', 'name' => 'MSI MAG B650 Tomahawk', 'stock' => 10, 'price' => 219.00, 'warehouse' => 'Warehouse A'],
            ['id' => 'P011', 'category' => 'Power Supply', 'name' => 'Corsair RM850x 850W', 'stock' => 14, 'price' => 149.99, 'warehouse' => 'Warehouse B'],
            ['id' => 'P012', 'category' => 'Power Supply', 'name' => 'EVGA SuperNOVA 750W', 'stock' => 9, 'price' => 119.99, 'warehouse' => 'Warehouse C'],
            ['id' => 'P013', 'category' => 'Case', 'name' => 'NZXT H5 Flow', 'stock' => 8, 'price' => 94.99, 'warehouse' => 'Warehouse A'],
            ['id' => 'P014', 'category' => 'Case', 'name' => 'Fractal Design North', 'stock' => 4, 'price' => 139.99, 'warehouse' => 'Warehouse B'],
            ['id' => 'P015', 'category' => 'Cooler', 'name' => 'Noctua NH-D15', 'stock' => 11, 'price' => 119.95, 'warehouse' => 'Warehouse C'],
            ['id' => 'P016', 'category' => 'CPU', 'name' => 'Intel Core i5-13400F', 'stock' => 14, 'price' => 209.00, 'warehouse' => 'Warehouse A'],
            ['id' => 'P017', 'category' => 'GPU', 'name' => 'NVIDIA RTX 4060', 'stock' => 12, 'price' => 299.99, 'warehouse' => 'Warehouse B'],
            ['id' => 'P018', 'category' => 'Motherboard', 'name' => 'Gigabyte B760M DS3H', 'stock' => 8, 'price' => 109.99, 'warehouse' => 'Warehouse C'],
            ['id' => 'P019', 'category' => 'RAM', 'name' => 'Kingston Fury 16GB DDR4', 'stock' => 30, 'price' => 45.00, 'warehouse' => 'Warehouse A'],
            ['id' => 'P020', 'category' => 'Power Supply', 'name' => 'Thermaltake Smart 600W', 'stock' => 15, 'price' => 49.99, 'warehouse' => 'Warehouse B']
        ];

        foreach ($items as $item) {
            // Give every seeded item sensible default reorder thresholds so
            // the Alerts & Reorders tab has real data out of the box:
            // min ~40% above current stock, max ~3x current stock.
            $item['minLimit'] = $item['minLimit'] ?? max(5, (int) round($item['stock'] * 0.6));
            $item['maxLimit'] = $item['maxLimit'] ?? max($item['minLimit'] + 10, (int) round($item['stock'] * 2.5) + 10);
            $item['auto_reorder'] = $item['auto_reorder'] ?? false;
            InventoryItem::create($item);
        }

        // Restoring Original System Logs
        $logs = [
            ['user' => "admin1", 'action' => "Transferred 20 units of Corsair DDR5 RAM kits from Zone A shelving to Zone C locker row 4.", 'created_at' => "2026-07-13 09:14:02"],
            ['user' => "manager2", 'action' => "Approved bulk restocking transaction intake for 10 units of Intel Core i9 CPUs.", 'created_at' => "2026-07-13 08:35:50"],
            ['user' => "admin4", 'action' => "Flagged 3 units of Seasonic Vertex Power Supplies as defective under vendor RMA tracking.", 'created_at' => "2026-07-13 07:11:15"],
            ['user' => "system_proc", 'action' => "Automated alert triggered: NVIDIA RTX 4090 stock level reached 0 units threshold limit.", 'created_at' => "2026-07-13 06:00:01"],
            ['user' => "admin1", 'action' => "Created master catalog entry layout model for PCIe Gen5 Storage lines.", 'created_at' => "2026-07-12 16:40:22"],
            ['user' => "manager1", 'action' => "Completed manual stock reconciliation count inside Central CPU storage cage.", 'created_at' => "2026-07-12 14:15:00"],
            ['user' => "admin2", 'action' => "Dispatched 5 units of ASUS RTX 4070 graphic boards to shipping floor.", 'created_at' => "2026-07-12 11:24:43"],
            ['user' => "admin3", 'action' => "Relocated 15 units of Samsung 990 Pro SSDs into priority distribution dispatch lanes.", 'created_at' => "2026-07-12 09:05:12"],
            ['user' => "manager2", 'action' => "Updated safety alert threshold metrics across baseline GPU stock values.", 'created_at' => "2026-07-11 15:30:00"],
            ['user' => "admin1", 'action' => "Logged manufacturer warranty certificate papers for inbound Kingston memory.", 'created_at' => "2026-07-11 10:22:18"]
        ];
        foreach ($logs as $log) { SystemLog::create($log); }

        // Restoring Pending Inspections
        QcInspection::create(['id' => 'REQ-I-101', 'op' => 'admin2', 'itemId' => 'P008', 'product' => 'WD Blue 4TB HDD', 'source' => 'Customer Aftersales', 'action' => 'Damaged - Move to Quarantine', 'status' => 'Pending', 'created_at' => '2026-07-13 09:15:00']);
        QcInspection::create(['id' => 'REQ-I-102', 'op' => 'admin1', 'itemId' => 'PRD-001', 'product' => 'Gigabyte M27Q Monitor', 'source' => 'Warehouse Transfer', 'action' => 'Good - Clear for Restock', 'status' => 'Pending', 'created_at' => '2026-07-13 10:30:00']);

        // Restoring Pending RMAs
        RmaRequest::create(['id' => 'REQ-R-201', 'op' => 'admin3', 'itemId' => 'PC-003', 'product' => 'NVIDIA RTX 4090', 'vendor' => 'NVIDIA', 'reasons' => 'Dead on Arrival (DOA), Failed QC Bench Test', 'status' => 'Pending', 'created_at' => '2026-07-13 11:05:00']);
        RmaRequest::create(['id' => 'REQ-R-202', 'op' => 'admin1', 'itemId' => 'P002', 'product' => 'AMD Ryzen 9 7950X', 'vendor' => 'AMD', 'reasons' => 'Physical Defect', 'status' => 'Pending', 'created_at' => '2026-07-13 11:45:00']);

        // Restoring Returns Audit Logs
        $audits = [
            ['op' => 'admin2', 'stream' => 'Inspection', 'info' => 'Fractal Design Meshify C (Source: Warehouse)', 'outcome' => 'Approved: Restocked', 'statusType' => 'success', 'created_at' => '2026-07-12 16:20:00'],
            ['op' => 'admin3', 'stream' => 'RMA', 'info' => 'MSI MPG A850G (Vendor: MSI - Reason: DOA)', 'outcome' => 'Approved: Returned to Mfg', 'statusType' => 'neutral', 'created_at' => '2026-07-12 15:10:00'],
            ['op' => 'admin1', 'stream' => 'Inspection', 'info' => 'Lian Li UNI FAN SL120', 'outcome' => 'Voided by Manager', 'statusType' => 'void', 'created_at' => '2026-07-12 14:05:00'],
            ['op' => 'admin2', 'stream' => 'RMA', 'info' => 'G.Skill Trident Z5 (Vendor: G.Skill - Reason: Missing Box)', 'outcome' => 'Approved: Returned to Mfg', 'statusType' => 'neutral', 'created_at' => '2026-07-11 09:30:00'],
            ['op' => 'admin1', 'stream' => 'Inspection', 'info' => 'Noctua NH-D15 Cooler (Source: Customer)', 'outcome' => 'Approved: Quarantined', 'statusType' => 'danger', 'created_at' => '2026-07-11 08:15:00']
        ];
        foreach ($audits as $audit) { ReturnsAuditLog::create($audit); }

        // Populate stock_alerts for any seeded items that are already
        // out-of-range, so the Alerts & Reorders tab has real data on
        // first load instead of sitting empty until the schedule/command
        // runs on its own.
        \Illuminate\Support\Facades\Artisan::call('stock:check-levels');
    }
}