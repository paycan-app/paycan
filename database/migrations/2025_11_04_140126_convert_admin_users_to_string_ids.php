<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create new temporary table with string IDs
        Schema::create('admin_users_new', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->string('role')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Copy all existing admin users with new prefixed string IDs
        $oldAdminUsers = DB::table('admin_users')->get();

        foreach ($oldAdminUsers as $oldUser) {
            DB::table('admin_users_new')->insert([
                'id' => 'adm__'.(string) Str::ulid(),
                'name' => $oldUser->name,
                'email' => $oldUser->email,
                'email_verified_at' => $oldUser->email_verified_at,
                'password' => $oldUser->password,
                'phone' => $oldUser->phone,
                'role' => $oldUser->role,
                'remember_token' => $oldUser->remember_token,
                'created_at' => $oldUser->created_at,
                'updated_at' => $oldUser->updated_at,
            ]);
        }

        // 3. Drop old table
        Schema::dropIfExists('admin_users');

        // 4. Rename new table to admin_users
        Schema::rename('admin_users_new', 'admin_users');

        // 5. Clear all sessions to force re-login
        DB::table('sessions')->truncate();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reversal would be complex and data-lossy, so we'll just error
        throw new RuntimeException('Cannot reverse admin_users ID conversion. This would lose data.');
    }
};
