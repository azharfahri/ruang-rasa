<?php

namespace Database\Seeders;

use App\Models\VariantType;
use Illuminate\Database\Seeder;

class VariantTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // AMERICANO (Product 1)
        VariantType::create([
            'product_id' => 1,
            'name' => 'Suhu',
            'input_type' => 'radio',
        ]);

        VariantType::create([
            'product_id' => 1,
            'name' => 'Ukuran',
            'input_type' => 'radio',
        ]);

        // CAPPUCCINO (Product 2)
        VariantType::create([
            'product_id' => 2,
            'name' => 'Suhu',
            'input_type' => 'radio',
        ]);

        VariantType::create([
            'product_id' => 2,
            'name' => 'Ukuran',
            'input_type' => 'radio',
        ]);

        VariantType::create([
            'product_id' => 2,
            'name' => 'Extra',
            'input_type' => 'checkbox',
        ]);

        // LATTE (Product 3)
        VariantType::create([
            'product_id' => 3,
            'name' => 'Suhu',
            'input_type' => 'radio',
        ]);

        VariantType::create([
            'product_id' => 3,
            'name' => 'Ukuran',
            'input_type' => 'radio',
        ]);

        VariantType::create([
            'product_id' => 3,
            'name' => 'Extra',
            'input_type' => 'checkbox',
        ]);

        // MATCHA LATTE (Product 4)
        VariantType::create([
            'product_id' => 4,
            'name' => 'Suhu',
            'input_type' => 'radio',
        ]);

        VariantType::create([
            'product_id' => 4,
            'name' => 'Ukuran',
            'input_type' => 'radio',
        ]);

        // CHOCOLATE (Product 5)
        VariantType::create([
            'product_id' => 5,
            'name' => 'Suhu',
            'input_type' => 'radio',
        ]);

        VariantType::create([
            'product_id' => 5,
            'name' => 'Ukuran',
            'input_type' => 'radio',
        ]);

        // FRENCH FRIES (Product 6)
        VariantType::create([
            'product_id' => 6,
            'name' => 'Ukuran',
            'input_type' => 'radio',
        ]);

        VariantType::create([
            'product_id' => 6,
            'name' => 'Extra Saus',
            'input_type' => 'checkbox',
        ]);

        // CHICKEN WINGS (Product 7)
        VariantType::create([
            'product_id' => 7,
            'name' => 'Level Pedas',
            'input_type' => 'radio',
        ]);

        VariantType::create([
            'product_id' => 7,
            'name' => 'Extra',
            'input_type' => 'checkbox',
        ]);

        // CHEESECAKE (Product 8)
        VariantType::create([
            'product_id' => 8,
            'name' => 'Topping',
            'input_type' => 'checkbox',
        ]);

        // ICE CREAM (Product 9)
        VariantType::create([
            'product_id' => 9,
            'name' => 'Rasa',
            'input_type' => 'radio',
        ]);

        VariantType::create([
            'product_id' => 9,
            'name' => 'Topping',
            'input_type' => 'checkbox',
        ]);
    }
}
