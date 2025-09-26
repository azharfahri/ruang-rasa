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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('casier_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('order_type', ['online_pickup', 'offline_dinein', 'offline_takeaway']);
            $table->enum('status', ['pending', 'processing', 'ready', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['pending', 'settlement', 'expire', 'cancel', 'failure', 'refund'])->default('pending');

            $table->string('midtrans_booking_code')->nullable()->unique(); // ID unik Midtrans dari transaksi pertama
            $table->timestamp('midtrans_expiry_time')->nullable();
            $table->string('pickup_code', 255)->nullable()->unique(); // Kode untuk pengambilan di store

            $table->decimal('total', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
