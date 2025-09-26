<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');

            $table->string('payment_method');
            $table->decimal('amount', 12, 2);
            $table->string('midtrans_id')->unique(); // ID Transaksi Midtrans

            // Status dari webhook Midtrans (bisa berbeda dari order.payment_status)
            $table->string('status');
            $table->json('raw_response')->nullable(); // Data webhook lengkap

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
