<?php

namespace Database\Seeders;

use App\Models\Attribute;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        $attributes = [
            // Elektronika uchun attributelar
            [
                'name' => 'Brand',
                'type' => 'select',
                'options' => json_encode(['Apple', 'Samsung', 'Xiaomi', 'Huawei', 'LG', 'Sony']),
                'category_id' => 1,
                'is_required' => true,
            ],
            [
                'name' => 'Model',
                'type' => 'text',
                'category_id' => 1,
                'is_required' => true,
            ],
            [
                'name' => 'Storage',
                'type' => 'select',
                'options' => json_encode(['32GB', '64GB', '128GB', '256GB', '512GB', '1TB']),
                'category_id' => 1,
                'is_required' => true,
            ],
            [
                'name' => 'Color',
                'type' => 'text',
                'category_id' => 1,
                'is_required' => true,
            ],
            [
                'name' => 'Warranty',
                'type' => 'boolean',
                'category_id' => 1,
                'is_required' => false,
            ],

            // Kiyim-kechak uchun attributelar
            [
                'name' => 'Size',
                'type' => 'select',
                'options' => json_encode(['XS', 'S', 'M', 'L', 'XL', 'XXL']),
                'category_id' => 2,
                'is_required' => true,
            ],
            [
                'name' => 'Color',
                'type' => 'text',
                'category_id' => 2,
                'is_required' => true,
            ],
            [
                'name' => 'Material',
                'type' => 'text',
                'category_id' => 2,
                'is_required' => true,
            ],
            [
                'name' => 'Gender',
                'type' => 'select',
                'options' => json_encode(['Male', 'Female', 'Unisex']),
                'category_id' => 2,
                'is_required' => true,
            ],

            // Uy-ro'zg'or uchun attributelar
            [
                'name' => 'Brand',
                'type' => 'select',
                'options' => json_encode(['LG', 'Samsung', 'Artel', 'Shivaki', 'Sony']),
                'category_id' => 3,
                'is_required' => true,
            ],
            [
                'name' => 'Warranty',
                'type' => 'number',
                'category_id' => 3,
                'is_required' => true,
            ],
            [
                'name' => 'Energy Class',
                'type' => 'select',
                'options' => json_encode(['A+++', 'A++', 'A+', 'A', 'B', 'C']),
                'category_id' => 3,
                'is_required' => false,
            ],

            // Transport uchun attributelar
            [
                'name' => 'Brand',
                'type' => 'select',
                'options' => json_encode(['Chevrolet', 'Toyota', 'Hyundai', 'Kia', 'Ford']),
                'category_id' => 4,
                'is_required' => true,
            ],
            [
                'name' => 'Year',
                'type' => 'number',
                'category_id' => 4,
                'is_required' => true,
            ],
            [
                'name' => 'Mileage',
                'type' => 'number',
                'category_id' => 4,
                'is_required' => true,
            ],
            [
                'name' => 'Fuel Type',
                'type' => 'select',
                'options' => json_encode(['Petrol', 'Diesel', 'Gas', 'Electric', 'Hybrid']),
                'category_id' => 4,
                'is_required' => true,
            ],
            [
                'name' => 'Transmission',
                'type' => 'select',
                'options' => json_encode(['Manual', 'Automatic']),
                'category_id' => 4,
                'is_required' => true,
            ],

            // Ko'chmas mulk uchun attributelar
            [
                'name' => 'Rooms',
                'type' => 'number',
                'category_id' => 5,
                'is_required' => true,
            ],
            [
                'name' => 'Floor',
                'type' => 'number',
                'category_id' => 5,
                'is_required' => true,
            ],
            [
                'name' => 'Total Floors',
                'type' => 'number',
                'category_id' => 5,
                'is_required' => true,
            ],
            [
                'name' => 'Area',
                'type' => 'number',
                'category_id' => 5,
                'is_required' => true,
            ],
            [
                'name' => 'Furnished',
                'type' => 'boolean',
                'category_id' => 5,
                'is_required' => false,
            ],
        ];

        foreach ($attributes as $attribute) {
            Attribute::create($attribute);
        }
    }
}
