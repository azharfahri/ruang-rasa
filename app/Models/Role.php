<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    protected static function booted()
    {
        static::deleting(function ($role) {
            if ($role->users()->exists()) {
                throw new \Exception(
                    "Role masih digunakan oleh pengguna lain, tidak bisa dihapus."
                );
            }
        });
    }
}
