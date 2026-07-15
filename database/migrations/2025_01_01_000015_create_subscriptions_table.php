<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            $table->string('user_id');
            $table->string('product_id');
            $table->string('product_price_id');

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->foreign('product_id')
                ->references('id')
                ->on('products');

            $table->foreign('product_price_id')
                ->references('id')
                ->on('product_prices');

            $table->foreignId('order_id')->constrained(); // Initial order
            $table->string('title'); // Name of the subscription
            $table->string('status'); // active, trialing, past_due, canceled, incomplete, incomplete_expired
            $table->string('gateway'); // stripe, paypal, square, etc.
            $table->string('gateway_subscription_id')->nullable(); // Gateway subscription ID
            $table->string('gateway_status')->nullable(); // Gateway-specific status
            $table->json('gateway_data')->nullable(); // Additional gateway-specific data
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('next_billing_date')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
