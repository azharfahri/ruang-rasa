<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('branch_id')
                ->constrained('branches')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->integer('stock')->default(0);

            $table->decimal('price_override', 12, 2)->nullable();
            // kalau cabang punya harga beda

            $table->enum('status', ['available', 'soldout'])
                ->default('available');

            $table->timestamps();

            // biar ga duplikat stok produk di cabang
            $table->unique(['branch_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_products');
    }
};
