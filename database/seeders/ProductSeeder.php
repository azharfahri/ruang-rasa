<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Americano',
            'slug' => 'americano',
            'description' => 'Espresso dengan air mineral',
            'price' => 15000,
            'image' => 'americano.jpg',
            'category_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
