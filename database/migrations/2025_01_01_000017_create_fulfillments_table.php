<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fulfillments', function (Blueprint $table) {
            // $table->id();
            $table->string('id', 50)->primary();

            $table->foreignId('order_id')->constrained();
            $table->string('status'); // pending, processing, completed, failed
            $table->string('type'); // digital, physical, service, subscription_access
            $table->string('tracking_id')->nullable();
            $table->string('provider')->nullable(); // UPS, FedEx, USPS, DHL, etc.
            $table->json('meta')->nullable(); // Additional meta information
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fulfillments');
    }
};
