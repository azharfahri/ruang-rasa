<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name' => 'Makanan Berat',
            'type' => 1,
            'slug' => 'makanan-berat',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Category::create([
            'name' => 'Snack',
            'type' => 1,
            'slug' => 'snack',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Category::create([
            'name' => 'Kopi',
            'type' => 2,
            'slug' => 'kopi',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Category::create([
            'name' => 'Non Kopi',
            'type' => 2,
            'slug' => 'non-kopi',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Category::create([
            'name' => 'Dessert',
            'type' => 3,
            'slug' => 'dessert',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Category::create([
            'name' => 'Addon',
            'type' => 4,
            'slug' => 'addon',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Category::create([
            'name' => 'Lainnya',
            'type' => 5,
            'slug' => 'lainnya',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
