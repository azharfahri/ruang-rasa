<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('variant_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('name');
            $table->enum('input_type', ['radio', 'checkbox'])->default('radio');
            $table->timestamps();
            $table->unique(['product_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variant_types');
    }
};
