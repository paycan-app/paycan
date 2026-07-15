<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('user_id')->constrained();

            $table->string('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->foreignId('order_id')->nullable()->constrained(); // Nullable for subscription payments
            $table->foreignId('subscription_id')->nullable()->constrained(); // Nullable for one-time payments
            $table->string('type'); // charge, refund, subscription_create, subscription_renew, subscription_update, subscription_cancel
            $table->string('status'); // pending, completed, failed, refunded
            $table->string('gateway'); // stripe, paypal, square, etc.
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('USD');
            $table->string('gateway_transaction_id'); // Gateway transaction ID
            $table->json('gateway_data')->nullable(); // Additional gateway-specific data
            $table->json('meta')->nullable(); // Additional metadata
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
