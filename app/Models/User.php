<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'branch_id', // tambahin kalau user bisa punya cabang
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // =====================
    //   RELASI USER
    // =====================

    // User bisa punya banyak role via pivot user_roles
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    // relasi pivotnya langsung
    public function userRoles()
    {
        return $this->hasMany(UserRole::class);
    }

    // user bisa terhubung ke cabang (opsional)
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // Customer orders
    public function customerOrders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    // Cashier orders
    public function cashierOrders()
    {
        return $this->hasMany(Order::class, 'cashier_id');
    }

    // cek role dengan mudah
    public function hasRole($roleName)
    {
        return $this->roles()->where('name', $roleName)->exists();
    }
}
