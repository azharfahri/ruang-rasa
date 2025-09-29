<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');

            $table->integer('quantity');
            $table->decimal('price', 12, 2); // Harga produk unit saat pesanan dibuat

            // Tambahan Field untuk Order Attributes (Menggunakan ENUM)
            $table->enum('temperature', ['Hot', 'Iced'])->nullable()->default(null);
            $table->enum('sugar_level', ['Normal', 'Less Sugar', 'No Sugar'])->nullable()->default('Normal');
            $table->enum('ice_level', ['Normal', 'Less Ice', 'No Ice'])->nullable()->default('Normal');

            // Detail Varian dan Notes disimpan dalam JSON/TEXT
            $table->json('variant_details')->nullable(); // [{"name": "Large", "impact": 5000}, ...]
            $table->text('notes')->nullable(); // Permintaan khusus

            $table->timestamps();
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
