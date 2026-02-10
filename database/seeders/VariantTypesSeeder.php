<?php

namespace Database\Seeders;

use App\Models\VariantType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VariantTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
    }
}
