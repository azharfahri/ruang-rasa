<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Container\Attributes\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Branch::create([
            'name' => 'Ruang Rasa Cibaduyut',
            'address' => 'Jalan Raya Cibaduyut, Cibaduyut Wetan, Bojongloa Kidul, Kota Bandung, Jawa Barat, Jawa, 40236, Indonesia',
            'latitude' => -6.9590676,
            'longitude' => 107.5934672,
            'open_time' => '09:00:00',
            'close_time' => '22:00:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
