<?php

namespace App\Console\Commands;

use App\Models\AdminUser;
use App\Models\User;
use Illuminate\Console\Command;

class MigrateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-admin-user {email=hassan.jahan@gmail.com}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate a user from the users table to the admin_users table';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("User with email '{$email}' not found in users table.");

            return self::FAILURE;
        }

        // Check if admin user already exists
        if (AdminUser::where('email', $email)->exists()) {
            $this->warn("AdminUser with email '{$email}' already exists. Skipping.");

            return self::SUCCESS;
        }

        // Create admin user from the existing user
        $adminUser = AdminUser::create([
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password, // Already hashed
            'email_verified_at' => $user->email_verified_at,
            'role' => 'super_admin',
            'phone' => null,
        ]);

        $this->info("Successfully migrated user '{$email}' to admin_users table.");
        $this->info("Admin User ID: {$adminUser->id}");
        $this->info("Role: {$adminUser->role}");

        $this->newLine();
        $this->comment('Note: The original user record in the users table was NOT deleted.');
        $this->comment('If you want to remove it, you can manually delete it from the database.');

        return self::SUCCESS;
    }
}
