<?php

namespace Database\Seeders;

use App\Models\VariantOption;
use Illuminate\Database\Seeder;

class VariantOptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ===== SUHU (SEMUA MINUMAN) =====
        foreach ([1, 3, 6, 9, 11] as $id) {
            VariantOption::create([
                'variant_type_id' => $id,
                'option_name' => 'Panas',
                'price_impact' => 0,
            ]);

            VariantOption::create([
                'variant_type_id' => $id,
                'option_name' => 'Dingin',
                'price_impact' => 0,
            ]);
        }

        // ===== UKURAN MINUMAN =====
        foreach ([2, 4, 7, 10, 12] as $id) {
            VariantOption::create([
                'variant_type_id' => $id,
                'option_name' => 'Small',
                'price_impact' => 0,
            ]);

            VariantOption::create([
                'variant_type_id' => $id,
                'option_name' => 'Medium',
                'price_impact' => 3000,
            ]);

            VariantOption::create([
                'variant_type_id' => $id,
                'option_name' => 'Large',
                'price_impact' => 5000,
            ]);
        }

        // ===== EXTRA MINUMAN =====
        foreach ([5, 8] as $id) {
            VariantOption::create([
                'variant_type_id' => $id,
                'option_name' => 'Extra Shot',
                'price_impact' => 5000,
            ]);

            VariantOption::create([
                'variant_type_id' => $id,
                'option_name' => 'Caramel Syrup',
                'price_impact' => 3000,
            ]);

            VariantOption::create([
                'variant_type_id' => $id,
                'option_name' => 'Whipped Cream',
                'price_impact' => 4000,
            ]);
        }

        // ===== FRIES UKURAN =====
        VariantOption::create([
            'variant_type_id' => 13,
            'option_name' => 'Regular',
            'price_impact' => 0,
        ]);

        VariantOption::create([
            'variant_type_id' => 13,
            'option_name' => 'Large',
            'price_impact' => 5000,
        ]);

        // ===== EXTRA SAUS =====
        VariantOption::create([
            'variant_type_id' => 14,
            'option_name' => 'Saus BBQ',
            'price_impact' => 2000,
        ]);

        VariantOption::create([
            'variant_type_id' => 14,
            'option_name' => 'Saus Keju',
            'price_impact' => 3000,
        ]);

        // ===== LEVEL PEDAS =====
        VariantOption::create([
            'variant_type_id' => 15,
            'option_name' => 'Level 1',
            'price_impact' => 0,
        ]);

        VariantOption::create([
            'variant_type_id' => 15,
            'option_name' => 'Level 2',
            'price_impact' => 0,
        ]);

        VariantOption::create([
            'variant_type_id' => 15,
            'option_name' => 'Level 3',
            'price_impact' => 0,
        ]);

        // ===== EXTRA WINGS =====
        VariantOption::create([
            'variant_type_id' => 16,
            'option_name' => 'Extra Sauce',
            'price_impact' => 3000,
        ]);

        // ===== TOPPING CHEESECAKE =====
        VariantOption::create([
            'variant_type_id' => 17,
            'option_name' => 'Strawberry',
            'price_impact' => 3000,
        ]);

        VariantOption::create([
            'variant_type_id' => 17,
            'option_name' => 'Chocolate',
            'price_impact' => 3000,
        ]);

        // ===== ICE CREAM RASA =====
        VariantOption::create([
            'variant_type_id' => 18,
            'option_name' => 'Vanilla',
            'price_impact' => 0,
        ]);

        VariantOption::create([
            'variant_type_id' => 18,
            'option_name' => 'Chocolate',
            'price_impact' => 0,
        ]);

        VariantOption::create([
            'variant_type_id' => 18,
            'option_name' => 'Strawberry',
            'price_impact' => 0,
        ]);

        // ===== ICE CREAM TOPPING =====
        VariantOption::create([
            'variant_type_id' => 19,
            'option_name' => 'Oreo Crumbs',
            'price_impact' => 2000,
        ]);

        VariantOption::create([
            'variant_type_id' => 19,
            'option_name' => 'Choco Chips',
            'price_impact' => 2000,
        ]);
    }
}
