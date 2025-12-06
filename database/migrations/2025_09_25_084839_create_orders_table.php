<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('cashier_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            $table->enum('order_type', ['online_pickup', 'offline_dinein', 'offline_takeaway']);
            $table->enum('status', ['pending', 'processing', 'ready', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['pending', 'settlement', 'expire', 'cancel', 'failure', 'refund'])->default('pending');

            $table->string('pickup_code', 255)->nullable()->unique();

            $table->decimal('total', 12, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
