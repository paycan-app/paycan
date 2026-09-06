<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->json('credit_allocations')->nullable()->after('trial_days');
            $table->string('credit_renewal_policy', 30)->default('accumulate')->after('credit_allocations');
        });
    }

    public function down(): void
    {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->dropColumn(['credit_allocations', 'credit_renewal_policy']);
        });
    }
};
