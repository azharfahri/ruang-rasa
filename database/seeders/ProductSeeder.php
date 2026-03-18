<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'product_code' => 'PRD-0001',
            'name'         => 'Americano',
            'slug'         => 'americano',
            'description'  => 'Espresso dengan air mineral',
            'price'        => 15000,
            'image'        => 'americano.jpg',
            'category_id'  => 3,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        Product::create([
            'product_code' => 'PRD-0002',
            'name'         => 'Cappuccino',
            'slug'         => 'cappuccino',
            'description'  => 'Espresso dengan susu dan foam',
            'price'        => 18000,
            'image'        => 'cappuccino.jpg',
            'category_id'  => 3,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        Product::create([
            'product_code' => 'PRD-0003',
            'name'         => 'Latte',
            'slug'         => 'latte',
            'description'  => 'Espresso dengan susu creamy',
            'price'        => 20000,
            'image'        => 'latte.jpg',
            'category_id'  => 3,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        Product::create([
            'product_code' => 'PRD-0004',
            'name'         => 'Matcha Latte',
            'slug'         => 'matcha-latte',
            'description'  => 'Minuman matcha dengan susu',
            'price'        => 22000,
            'image'        => 'matcha.jpg',
            'category_id'  => 4,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        Product::create([
            'product_code' => 'PRD-0005',
            'name'         => 'Chocolate',
            'slug'         => 'chocolate',
            'description'  => 'Minuman coklat hangat/dingin',
            'price'        => 20000,
            'image'        => 'chocolate.jpg',
            'category_id'  => 4,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        Product::create([
            'product_code' => 'PRD-0006',
            'name'         => 'French Fries',
            'slug'         => 'french-fries',
            'description'  => 'Kentang goreng crispy',
            'price'        => 15000,
            'image'        => 'fries.jpg',
            'category_id'  => 2,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        Product::create([
            'product_code' => 'PRD-0007',
            'name'         => 'Chicken Wings',
            'slug'         => 'chicken-wings',
            'description'  => 'Sayap ayam dengan saus spesial',
            'price'        => 25000,
            'image'        => 'wings.jpg',
            'category_id'  => 2,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        Product::create([
            'product_code' => 'PRD-0008',
            'name'         => 'Cheesecake',
            'slug'         => 'cheesecake',
            'description'  => 'Dessert keju lembut',
            'price'        => 23000,
            'image'        => 'cheesecake.jpg',
            'category_id'  => 5,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        Product::create([
            'product_code' => 'PRD-0009',
            'name'         => 'Ice Cream',
            'slug'         => 'ice-cream',
            'description'  => 'Es krim berbagai rasa',
            'price'        => 18000,
            'image'        => 'icecream.jpg',
            'category_id'  => 5,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        Product::create([
            'product_code' => 'PRD-0010',
            'name'         => 'Extra Shot Espresso',
            'slug'         => 'extra-shot',
            'description'  => 'Tambahan espresso shot',
            'price'        => 5000,
            'image'        => 'extra-shot.jpg',
            'category_id'  => 6,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }
}
