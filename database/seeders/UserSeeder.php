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
            'name' => 'Admin Ruang Rasa',
            'email' => 'admin@ruangrasa.com',
            'password' => Hash::make('adminruangrasa'),
        ]);

        // Kasir
        $kasir = User::create([
            'name' => 'Kasir Ruang Rasa',
            'email' => 'kasir@ruangrasa.com',
            'password' => Hash::make('kasirruangrasa'),
        ]);
    }
}
