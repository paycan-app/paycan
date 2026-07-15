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

            // $table->foreignId('user_id')->constrained();
            // $table->foreignId('product_id')->constrained();
            // $table->foreignId('product_price_id')->constrained();
            $table->string('order_number')->unique();
            $table->string('status'); // pending, processing, completed, failed, cancelled, refunded
            $table->decimal('total', 10, 2);
            $table->string('currency')->default('USD');
            $table->decimal('tax', 10, 2)->default(0);
            $table->string('billing_email');
            $table->string('billing_name');
            $table->string('billing_address')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_zipcode')->nullable();
            $table->string('billing_country')->nullable();
            $table->string('gateway'); // stripe, paypal, square, etc.
            $table->string('gateway_order_id')->nullable(); // Gateway-specific order ID
            $table->json('gateway_data')->nullable(); // Additional gateway-specific data
            $table->text('customer_note')->nullable();
            $table->integer('quantity')->default(1);
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
