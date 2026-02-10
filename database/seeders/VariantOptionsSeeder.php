<?php

namespace Database\Seeders;

use App\Models\VariantOption;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VariantOptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // opsi untuk Suhu
        VariantOption::create([
            'variant_type_id' => 1,
            'option_name' => 'Panas',
            'price_impact' => 0,
        ]);

        VariantOption::create([
            'variant_type_id' => 1,
            'option_name' => 'Dingin',
            'price_impact' => 0,
        ]);

        // opsi untuk Ukuran
        VariantOption::create([
            'variant_type_id' => 2,
            'option_name' => 'Kecil',
            'price_impact' => 0,
        ]);

        VariantOption::create([
            'variant_type_id' => 2,
            'option_name' => 'Sedang',
            'price_impact' => 3000,
        ]);

        VariantOption::create([
            'variant_type_id' => 2,
            'option_name' => 'Besar',
            'price_impact' => 5000,
        ]);
    }
}
