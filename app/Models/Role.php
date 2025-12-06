<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    // Relasi ke pivot user_roles
    public function userRoles()
    {
        return $this->hasMany(UserRole::class);
    }

    // Relasi langsung ke user lewat pivot
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }
}
