<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@ruangrasa.com')->first();
        $kasir = User::where('email', 'kasir@ruangrasa.com')->first();

        $roleAdmin  = Role::where('name', 'admin')->first();
        $roleKasir  = Role::where('name', 'cashier')->first();

        $admin->roles()->attach($roleAdmin->id);
        $kasir->roles()->attach($roleKasir->id);
    }
}
