<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test admin user
        AdminUser::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'role' => 'super_admin',
        ]);

        // Create test regular user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            ProductSeeder::class,
            OrderSeeder::class,
            FulfillmentSeeder::class,
        ]);
    }
}
