<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => [
                    'en' => 'Elektronika',
                    'ru' => 'Elektronika ru'
                ],
                'description' => ['en' => 'Telefonlar, kompyuterlar va boshqa elektronika mahsulotlari'],
                'slug' => Str::slug('Elektronika'),
            ],
            [
                'name' => [
                    'en' => 'Kiyim-kechak',
                    'ru' => 'Kiyim-kechak ru'
                ],
                'description' => ['en' => 'Erkaklar, ayollar va bolalar uchun kiyimlar'],
                'slug' => Str::slug('Kiyim-kechak'),
            ],
            [
                'name' => [
                    'en' => 'Uy-ro\'zg\'or',
                    'ru' => 'Uy-ro\'zg\'or ru'
                ],
                'description' => ['en' => 'Uy uchun jihozlar va texnika'],
                'slug' => Str::slug('Uy-ro\'zg\'or'),
            ],
            [
                'name' => [
                    'en' => 'Transport',
                    'ru' => 'Uy-ro\'zg\'or ru'
                ],
                'description' => ['en' => 'Avtomobillar va boshqa transport vositalari'],
                'slug' => Str::slug('Transport'),
            ],
            [
                'name' => [
                    'en' => 'Ko\'chmas mulk',
                    'en' => 'Ko\'chmas mulk ru'
                ],
                'description' => ['en' => 'Uylar, kvartiralr va boshqa ko\'chmas mulklar'],
                'slug' => Str::slug('Ko\'chmas mulk'),
            ],
            [
                'name' => ['en' => 'Ish topish'],
                'description' => ['en' => 'Ish topish va texnika'],
                'slug' => Str::slug('Ish topish'),
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
