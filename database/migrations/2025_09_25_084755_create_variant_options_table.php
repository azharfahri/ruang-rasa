<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('variant_options', function (Blueprint $table) {
            $table->id();
            // Menghubungkan Opsi Varian ke Tipe Varian
            $table->foreignId('variant_type_id')->constrained('variant_types')->onDelete('cascade');

            // Nama Pilihan (e.g., Large, 50%, Hot, Tambah Keju)
            $table->string('option_name');

            // Dampak harga (bisa positif atau negatif)
            $table->decimal('price_impact', 12, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variant_options');
    }
};
