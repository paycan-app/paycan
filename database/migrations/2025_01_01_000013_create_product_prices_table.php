<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->string('id', 50)->primary();

            $table->string('product_id');
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');

            // $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('title'); // Basic, Premium, Annual, etc.
            $table->string('slug')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('USD'); // USD, EUR, GBP, etc.
            $table->string('billing_period')->default('once'); // once, daily, weekly, monthly, yearly
            $table->integer('trial_days')->default(0);
            $table->json('gateway_data')->nullable(); // Store gateway-specific IDs and data as JSON
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

        });

    }

    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};
