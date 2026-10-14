<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Wireless Ergonomic Mouse',
                'sku' => 'PROD-001',
                'description' => 'Comfortable wireless mouse with ergonomic design and long battery life.',
                'category' => 'Peripherals',
                'quantity' => 20,
                'reorder_level' => 10,
                'unit_price' => 2490.00,
                'supplier' => 'TechSource Inc.',
            ],
            [
                'name' => 'Mechanical Gaming Keyboard',
                'sku' => 'PROD-002',
                'description' => 'RGB mechanical keyboard with blue switches, ideal for gaming and typing.',
                'category' => 'Peripherals',
                'quantity' => 8,
                'reorder_level' => 10,
                'unit_price' => 3990.00,
                'supplier' => 'TechSource Inc.',
            ],
            [
                'name' => '27-inch 4K QLED Monitor',
                'sku' => 'PROD-003',
                'description' => 'Ultra HD 4K QLED monitor with vibrant colors and thin bezels.',
                'category' => 'Displays',
                'quantity' => 5,
                'reorder_level' => 5,
                'unit_price' => 36500.00,
                'supplier' => 'DisplayWorld',
            ],
            [
                'name' => 'USB-C Docking Station',
                'sku' => 'PROD-004',
                'description' => 'Multi-port USB-C docking station with HDMI, Ethernet, and power delivery.',
                'category' => 'Accessories',
                'quantity' => 3,
                'reorder_level' => 8,
                'unit_price' => 450.00,
                'supplier' => 'ConnectPro',
            ],
            [
                'name' => 'Noise-Canceling Headphones',
                'sku' => 'PROD-005',
                'description' => 'Premium over-ear headphones with active noise cancellation.',
                'category' => 'Audio',
                'quantity' => 0,
                'reorder_level' => 5,
                'unit_price' => 5990.00,
                'supplier' => 'SoundMax',
            ],
            [
                'name' => 'HD Webcam 1080p',
                'sku' => 'PROD-006',
                'description' => 'Full HD 1080p webcam with built-in microphone for video calls.',
                'category' => 'Peripherals',
                'quantity' => 13,
                'reorder_level' => 8,
                'unit_price' => 3000.00,
                'supplier' => 'TechSource Inc.',
            ],
            [
                'name' => 'Hard Drive 1TB',
                'sku' => 'PROD-007',
                'description' => 'External 1TB hard drive, USB 3.0, portable and reliable.',
                'category' => 'Storage',
                'quantity' => 15,
                'reorder_level' => 10,
                'unit_price' => 1200.00,
                'supplier' => 'DataStore',
            ],
            [
                'name' => 'Solid State Drive 500GB',
                'sku' => 'PROD-008',
                'description' => 'High-speed 500GB SSD for faster boot times and file transfers.',
                'category' => 'Storage',
                'quantity' => 20,
                'reorder_level' => 10,
                'unit_price' => 2300.00,
                'supplier' => 'DataStore',
            ],
            [
                'name' => 'Wireless Bluetooth Speaker',
                'sku' => 'PROD-009',
                'description' => 'Portable Bluetooth speaker with deep bass and 12-hour battery.',
                'category' => 'Audio',
                'quantity' => 7,
                'reorder_level' => 6,
                'unit_price' => 1890.00,
                'supplier' => 'SoundMax',
            ],
            [
                'name' => 'Laptop Stand Aluminum',
                'sku' => 'PROD-010',
                'description' => 'Adjustable aluminum laptop stand for better ergonomics.',
                'category' => 'Accessories',
                'quantity' => 12,
                'reorder_level' => 8,
                'unit_price' => 990.00,
                'supplier' => 'ConnectPro',
            ],
            [
                'name' => '24-inch IPS Monitor',
                'sku' => 'PROD-011',
                'description' => 'Full HD IPS monitor with wide viewing angles.',
                'category' => 'Displays',
                'quantity' => 9,
                'reorder_level' => 6,
                'unit_price' => 8900.00,
                'supplier' => 'DisplayWorld',
            ],
            [
                'name' => 'USB Flash Drive 128GB',
                'sku' => 'PROD-012',
                'description' => 'High-speed USB 3.1 flash drive, 128GB capacity.',
                'category' => 'Storage',
                'quantity' => 25,
                'reorder_level' => 15,
                'unit_price' => 650.00,
                'supplier' => 'DataStore',
            ],
            [
                'name' => 'Gaming Mouse Pad XXL',
                'sku' => 'PROD-013',
                'description' => 'Extended gaming mouse pad with non-slip base.',
                'category' => 'Accessories',
                'quantity' => 18,
                'reorder_level' => 10,
                'unit_price' => 450.00,
                'supplier' => 'TechSource Inc.',
            ],
            [
                'name' => 'Wireless Earbuds Pro',
                'sku' => 'PROD-014',
                'description' => 'True wireless earbuds with noise cancellation and charging case.',
                'category' => 'Audio',
                'quantity' => 4,
                'reorder_level' => 8,
                'unit_price' => 3490.00,
                'supplier' => 'SoundMax',
            ],
            [
                'name' => 'USB-C Hub 7-in-1',
                'sku' => 'PROD-015',
                'description' => 'Compact 7-in-1 USB-C hub with HDMI, USB-A, SD card reader.',
                'category' => 'Accessories',
                'quantity' => 11,
                'reorder_level' => 7,
                'unit_price' => 1290.00,
                'supplier' => 'ConnectPro',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}