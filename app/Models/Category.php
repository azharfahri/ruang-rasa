<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'type',
        'slug', // Tambahkan slug
    ];

    protected $casts = [
        'type' => 'string',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // --- Accessor/Mutator (Opsional tapi direkomendasikan) ---

    /**
     * Mutator untuk memastikan slug dibuat secara otomatis saat nama diatur.
     * Anda perlu menginstal paket seperti 'spatie/laravel-sluggable' atau menggunakan helper string Laravel.
     * Contoh menggunakan helper string Laravel (Pastikan di-use: use Illuminate\Support\Str; )
     */
    /*
    public function setNameAttribute(string $value): void
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }
    */
}
