<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('wallet_id', 50);
            $table->foreign('wallet_id')
                ->references('id')
                ->on('wallets')
                ->onDelete('cascade');

            $table->string('user_id', 50);
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->string('type', 20); // 'credit', 'debit'
            $table->string('action', 50); // 'subscription_grant', 'subscription_renewal', 'order_purchase', 'usage', 'manual_adjustment', 'refund'
            $table->decimal('amount', 14, 4);
            $table->decimal('balance_after', 14, 4);
            $table->string('reference_id', 100)->nullable(); // e.g. subscription_id, order_id, agent run/trace id
            $table->string('description', 255)->nullable();
            $table->json('meta')->nullable(); // e.g. token counts, model names, execution data
            $table->timestamps();

            $table->index(['wallet_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('reference_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
