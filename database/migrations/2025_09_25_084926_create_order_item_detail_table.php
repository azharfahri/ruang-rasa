<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_item_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained('order_items')->onDelete('cascade');
            $table->foreignId('variant_type_id')->constrained('variant_types')->onDelete('restrict');
            $table->foreignId('variant_option_id')->constrained('variant_options')->onDelete('restrict');
            $table->decimal('price_impact', 12, 2)->default(0.00);
            $table->timestamps();

            $table->unique(['order_item_id', 'variant_option_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_details');
    }
};
