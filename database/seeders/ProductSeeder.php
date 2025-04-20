<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Wireless Mouse',
                'description' => 'Ergonomic wireless mouse with adjustable DPI.',
                'price' => 25,
                'quantity' => 50,
                'discount' => 5,
                'image' => 'images/products/mouse.jpg',
            ],
            [
                'name' => 'Bluetooth Headphones',
                'description' => 'Noise-cancelling over-ear headphones.',
                'price' => 80,
                'quantity' => 30,
                'discount' => 5,
                'image' => 'images/products/headphones.jpg',
            ],
            [
                'name' => 'USB-C Cable',
                'description' => 'Durable 2m USB-C charging cable.',
                'price' => 15,
                'quantity' => 100,
                'discount' => 10,
                'image' => 'images/products/usb-c.jpg',
            ],
            [
                'name' => 'Laptop Stand',
                'description' => 'Adjustable aluminum laptop stand.',
                'price' => 35,
                'quantity' => 20,
                'discount' => 8,
                'image' => 'images/products/laptop-stand.jpg',
            ],
            [
                'name' => 'Mechanical Keyboard',
                'description' => 'RGB backlit mechanical keyboard.',
                'price' => 120,
                'quantity' => 15,
                'discount' => 9,
                'image' => 'images/products/keyboard.jpg',
            ],
            [
                'name' => 'Portable SSD 1TB',
                'description' => 'High-speed portable SSD with 1TB storage.',
                'price' => 150,
                'quantity' => 10,
                'discount' => 78,
                'image' => 'images/products/ssd.jpg',
            ],
            [
                'name' => 'Smartphone Holder',
                'description' => 'Flexible smartphone holder for desks.',
                'price' => 12,
                'quantity' => 80,
                'discount' => 43,
                'image' => 'images/products/phone-holder.jpg',
            ],
            [
                'name' => 'Wireless Charger',
                'description' => 'Fast wireless charging pad.',
                'price' => 30,
                'quantity' => 40,
                'discount' => 5,
                'image' => 'images/products/charger.jpg',
            ],
            [
                'name' => 'Gaming Mouse Pad',
                'description' => 'Large RGB gaming mouse pad.',
                'price' => 20,
                'quantity' => 60,
                'discount' => 5,
                'image' => 'images/products/mouse-pad.jpg',
            ],
            [
                'name' => 'Webcam 1080p',
                'description' => 'Full HD webcam with built-in microphone.',
                'price' => 45,
                'quantity' => 25,
                'discount' => 0,
                'image' => 'images/products/webcam.jpg',
            ],
        ];

        foreach ($products as $product) {
            Product::create([
                'name' => $product['name'],
                'slug' => Str::slug($product['name']),
                'description' => $product['description'],
                'price' => $product['price'],
                'quantity' => $product['quantity'],
                'discount' => $product['discount'],
                'image' => $product['image'],
            ]);
        }
    }
}
