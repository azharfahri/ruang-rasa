<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        $admin = User::create([
            'name' => ' Admin Ruang Rasa',
            'email' => 'admin@ruangrasa.com',
            'password' => Hash::make('adminruangrasa'),
        ]);

        // AdminCabang
        $admincabangcibaduyut = User::create([
            'name' => 'Admin Cabang Cibaduyut',
            'email' => 'admincibaduyut@ruangrasa.com',
            'password' => Hash::make('admincibaduyut'),
            'branch_id' => '1',
        ]);

        // Kasir
        $kasir = User::create([
            'name' => 'Kasir Ruang Rasa Cibaduyut',
            'email' => 'kasircibaduyut@ruangrasa.com',
            'password' => Hash::make('kasircibaduyut'),
            'branch_id' => '1',
        ]);
    }
}
