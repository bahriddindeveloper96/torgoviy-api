<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Elektronika',
                'description' => 'Telefonlar, kompyuterlar va boshqa elektronika mahsulotlari',
            ],
            [
                'name' => 'Kiyim-kechak',
                'description' => 'Erkaklar, ayollar va bolalar uchun kiyimlar',
            ],
            [
                'name' => 'Uy-ro\'zg\'or',
                'description' => 'Uy uchun jihozlar va texnika',
            ],
            [
                'name' => 'Transport',
                'description' => 'Avtomobillar va boshqa transport vositalari',
            ],
            [
                'name' => 'Ko\'chmas mulk',
                'description' => 'Uylar, kvartiralr va boshqa ko\'chmas mulklar',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
