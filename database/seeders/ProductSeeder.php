<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use App\Models\Attribute;
use App\Models\ProductAttribute;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        
        $products = [
            // Elektronika
            [
                'title' => 'iPhone 13',
                'description' => 'Yangi iPhone 13, 128GB xotira',
                'price' => 999.99,
                'category_id' => 1,
                'condition' => 'new',
                'location' => 'Tashkent',
                'images' => [
                    ['image' => 'iphone13_1.jpg', 'is_primary' => true, 'order' => 0],
                    ['image' => 'iphone13_2.jpg', 'is_primary' => false, 'order' => 1],
                ],
                'attributes' => [
                    'Brand' => 'Apple',
                    'Model' => 'iPhone 13',
                    'Storage' => '128GB',
                    'Color' => 'Midnight',
                    'Warranty' => 'true',
                ],
            ],
            [
                'title' => 'Samsung Galaxy S21',
                'description' => 'Samsung Galaxy S21, yaxshi holatda',
                'price' => 599.99,
                'category_id' => 1,
                'condition' => 'used',
                'location' => 'Samarqand',
                'images' => [
                    ['image' => 'galaxy_s21.jpg', 'is_primary' => true, 'order' => 0],
                ],
                'attributes' => [
                    'Brand' => 'Samsung',
                    'Model' => 'Galaxy S21',
                    'Storage' => '256GB',
                    'Color' => 'Phantom Black',
                    'Warranty' => 'false',
                ],
            ],
            
            // Kiyim-kechak
            [
                'title' => 'Klassik kostyum',
                'description' => 'Erkaklar uchun klassik kostyum',
                'price' => 199.99,
                'category_id' => 2,
                'condition' => 'new',
                'location' => 'Tashkent',
                'images' => [
                    ['image' => 'suit_1.jpg', 'is_primary' => true, 'order' => 0],
                    ['image' => 'suit_2.jpg', 'is_primary' => false, 'order' => 1],
                ],
                'attributes' => [
                    'Size' => 'L',
                    'Color' => 'Navy Blue',
                    'Material' => 'Wool',
                    'Gender' => 'Male',
                ],
            ],
            
            // Uy-ro'zg'or
            [
                'title' => 'Muzlatgich',
                'description' => 'LG muzlatgich, 2 yil kafolat',
                'price' => 799.99,
                'category_id' => 3,
                'condition' => 'new',
                'location' => 'Buxoro',
                'images' => [
                    ['image' => 'fridge_1.jpg', 'is_primary' => true, 'order' => 0],
                ],
                'attributes' => [
                    'Brand' => 'LG',
                    'Warranty' => '24',
                    'Energy Class' => 'A++',
                ],
            ],
            
            // Transport
            [
                'title' => 'Chevrolet Malibu',
                'description' => '2022 yil, ideal holatda',
                'price' => 25000.00,
                'category_id' => 4,
                'condition' => 'used',
                'location' => 'Tashkent',
                'images' => [
                    ['image' => 'malibu_1.jpg', 'is_primary' => true, 'order' => 0],
                    ['image' => 'malibu_2.jpg', 'is_primary' => false, 'order' => 1],
                    ['image' => 'malibu_3.jpg', 'is_primary' => false, 'order' => 2],
                ],
                'attributes' => [
                    'Brand' => 'Chevrolet',
                    'Year' => '2022',
                    'Mileage' => '15000',
                    'Fuel Type' => 'Petrol',
                    'Transmission' => 'Automatic',
                ],
            ],
            
            // Ko'chmas mulk
            [
                'title' => '3 xonali kvartira',
                'description' => 'Chilonzorda 3 xonali kvartira, ta\'mirdan chiqqan',
                'price' => 75000.00,
                'category_id' => 5,
                'condition' => 'used',
                'location' => 'Tashkent',
                'images' => [
                    ['image' => 'apartment_1.jpg', 'is_primary' => true, 'order' => 0],
                    ['image' => 'apartment_2.jpg', 'is_primary' => false, 'order' => 1],
                ],
                'attributes' => [
                    'Rooms' => '3',
                    'Floor' => '4',
                    'Total Floors' => '9',
                    'Area' => '85',
                    'Furnished' => 'true',
                ],
            ],
        ];

        foreach ($products as $productData) {
            $attributes = $productData['attributes'];
            $images = $productData['images'];
            unset($productData['attributes'], $productData['images']);
            
            // Mahsulotni yaratish
            $user = $users->random();
            $productData['user_id'] = $user->id;
            $product = Product::create($productData);
            
            // Attributelarni qo'shish
            foreach ($attributes as $name => $value) {
                $attribute = Attribute::where('name', $name)
                    ->where('category_id', $product->category_id)
                    ->first();
                
                if ($attribute) {
                    ProductAttribute::create([
                        'product_id' => $product->id,
                        'attribute_id' => $attribute->id,
                        'value' => $value,
                    ]);
                }
            }

            // Rasmlarni qo'shish
            foreach ($images as $imageData) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $imageData['image'],
                    'is_primary' => $imageData['is_primary'],
                    'order' => $imageData['order'],
                ]);
            }
        }
    }
}
