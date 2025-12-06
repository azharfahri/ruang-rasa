<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserRole extends Model
{
    use HasFactory;

    protected $table = 'user_roles';

    protected $fillable = [
        'user_id',
        'role_id',
    ];

    // relasi user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // relasi role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
