<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',

        // Kolom kaku (temperature, sugar_level, ice_level, variant_details) DIHAPUS.

        'notes', // Tetap dipertahankan
    ];

    protected $casts = [
        'price' => 'decimal:2',
        // Casting untuk variant_details dan ENUMs lainnya DIHAPUS.
    ];

    // Konstanta ENUM Dihapus, karena validasi sekarang didasarkan pada data VariantOption.
    // public const TEMPERATURES = ['Hot', 'Iced'];
    // ...

    // --- Relasi ---

    /**
     * Relasi: Item dimiliki oleh satu Order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi: Item merujuk ke satu Product (untuk mendapatkan nama/gambar).
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi BARU: Item memiliki banyak detail varian yang dipilih.
     * Ini menghubungkan OrderItem ke OrderItemDetail (Langkah 6).
     */
    public function details(): HasMany
    {
        return $this->hasMany(OrderItemDetail::class);
    }

    /**
     * Relasi BANTUAN: Memungkinkan akses langsung ke Opsi Varian yang dipilih.
     * Menggunakan HasManyThrough melalui OrderItemDetail.
     */
    public function selectedOptions()
    {
        return $this->hasManyThrough(
            VariantOption::class,
            OrderItemDetail::class,
            'order_item_id', // Kunci asing pada tabel OrderItemDetail
            'id',            // Kunci pada tabel VariantOption
            'id',            // Kunci lokal pada tabel OrderItem
            'variant_option_id' // Kunci pada tabel OrderItemDetail yang merujuk VariantOption
        );
    }
}
