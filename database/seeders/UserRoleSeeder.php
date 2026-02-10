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
        $admincabang = User::where('email', 'admincibaduyut@ruangrasa.com')->first();
        $kasir = User::where('email', 'kasircibaduyut@ruangrasa.com')->first();

        $roleAdmin  = Role::where('name', 'admin')->first();
        $roleAdminCabang  = Role::where('name', 'admincabang')->first();
        $roleKasir  = Role::where('name', 'cashier')->first();

        $admin->roles()->attach($roleAdmin->id);
        $admincabang->roles()->attach($roleAdminCabang->id);
        $kasir->roles()->attach($roleKasir->id);
    }
}
