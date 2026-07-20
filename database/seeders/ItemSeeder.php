<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['id' => 101, 'name' => 'Intel Core i9-14900K', 'category' => 'Processor', 'qty' => 14, 'price' => 529.99, 'warehouse' => 'Warehouse A', 'location' => 'Shelf A-01', 'status' => 'Active', 'desc' => '24 Cores desktop processor configuration.'],
            ['id' => 102, 'name' => 'AMD Ryzen 7 7800X3D', 'category' => 'Processor', 'qty' => 28, 'price' => 369.00, 'warehouse' => 'Warehouse A', 'location' => 'Shelf A-02', 'status' => 'Active', 'desc' => '8 Cores with 3D V-Cache Gaming CPU.'],
            ['id' => 103, 'name' => 'NVIDIA RTX 4090 Founders Edition', 'category' => 'Graphics Card', 'qty' => 5, 'price' => 1599.99, 'warehouse' => 'Warehouse B', 'location' => 'Bay B-01', 'status' => 'Active', 'desc' => '24GB VRAM high end card configuration.'],
            ['id' => 104, 'name' => 'ASUS ROG Strix RTX 4070 Ti Super', 'category' => 'Graphics Card', 'qty' => 0, 'price' => 849.99, 'warehouse' => 'Warehouse B', 'location' => 'Bay B-03', 'status' => 'Out-of-Stock', 'desc' => '16GB VRAM quiet cooling layout fans.'],
            ['id' => 105, 'name' => 'Corsair Vengeance DDR5 32GB (2x16GB)', 'category' => 'Memory', 'warehouse' => 'Warehouse C', 'location' => 'Aisle C-01', 'qty' => 45, 'price' => 114.99, 'status' => 'Active', 'desc' => '6000MHz desktop computer RAM pack.'],
            ['id' => 106, 'name' => 'G.Skill Trident Z5 RGB 64GB DDR5', 'category' => 'Memory', 'warehouse' => 'Warehouse C', 'location' => 'Aisle C-02', 'qty' => 20, 'price' => 219.99, 'status' => 'Active', 'desc' => '6400MHz high capacity modules.'],
            ['id' => 107, 'name' => 'Samsung 990 Pro NVMe SSD 2TB', 'category' => 'Storage', 'warehouse' => 'Warehouse C', 'location' => 'Bin D-01', 'qty' => 60, 'price' => 169.99, 'status' => 'Active', 'desc' => 'Fast solid state hard drive disk stick.'],
            ['id' => 108, 'name' => 'Crucial T700 Gen5 SSD 1TB', 'category' => 'Storage', 'warehouse' => 'Warehouse C', 'location' => 'Bin D-04', 'qty' => 18, 'price' => 149.50, 'status' => 'Active', 'desc' => 'Gen5 performance slot SSD component.'],
            ['id' => 109, 'name' => 'MSI MAG Z790 Tomahawk WiFi', 'category' => 'Motherboard', 'warehouse' => 'Warehouse A', 'location' => 'Shelf E-01', 'qty' => 8, 'price' => 239.99, 'status' => 'Active', 'desc' => 'ATX size board matching Intel chips.'],
            ['id' => 110, 'name' => 'ASUS TUF Gaming X670E-Plus', 'category' => 'Motherboard', 'warehouse' => 'Warehouse A', 'location' => 'Shelf E-02', 'qty' => 11, 'price' => 279.99, 'status' => 'Active', 'desc' => 'AMD AM5 gaming base motherboard.'],
            ['id' => 111, 'name' => 'Corsair RM1000x 1000W PSU', 'category' => 'Power Supply', 'warehouse' => 'Warehouse B', 'location' => 'Bay F-01', 'qty' => 25, 'price' => 189.99, 'status' => 'Active', 'desc' => '1000 Watt full modular energy brick.'],
            ['id' => 112, 'name' => 'Seasonic Focus GX-850 Gold', 'category' => 'Power Supply', 'warehouse' => 'Warehouse B', 'location' => 'Bay F-02', 'qty' => 32, 'price' => 139.99, 'status' => 'Active', 'desc' => '850 Watt gold rated electricity delivery.'],
            ['id' => 113, 'name' => 'Intel Core i5-13400F', 'category' => 'Processor', 'warehouse' => 'Warehouse A', 'location' => 'Shelf A-04', 'qty' => 22, 'price' => 199.99, 'status' => 'Discontinued', 'desc' => 'Older standard budget processing chip.'],
            ['id' => 114, 'name' => 'AMD Radeon RX 7800 XT', 'category' => 'Graphics Card', 'warehouse' => 'Warehouse B', 'location' => 'Bay B-05', 'qty' => 15, 'price' => 499.99, 'status' => 'Active', 'desc' => '16GB Mid range performance graphics setup.'],
            ['id' => 115, 'name' => 'Intel Optane Memory M10 32GB', 'category' => 'Storage', 'warehouse' => 'Warehouse C', 'location' => 'Bin D-10', 'qty' => 3, 'price' => 45.00, 'status' => 'Archived', 'desc' => 'Legacy drive acceleration card unit.'],
            ['id' => 116, 'name' => 'Deepcool LS720 SE 360mm AIO', 'category' => 'Power Supply', 'warehouse' => 'Warehouse A', 'location' => 'Bay G-01', 'qty' => 12, 'price' => 112.50, 'status' => 'Active', 'desc' => 'Liquid CPU cooler hardware bundle.']
        ];

        foreach ($items as $item) {
            Item::updateOrCreate(['id' => $item['id']], $item);
        }
    }
}