<?php

namespace App\Console\Commands;

use App\Models\AdminUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class MakeAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user for the Filament admin panel';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Creating a new admin user...');
        $this->newLine();

        // Prompt for name
        $name = text(
            label: 'Name',
            placeholder: 'John Doe',
            required: true
        );

        // Prompt for email
        $email = text(
            label: 'Email',
            placeholder: 'admin@example.com',
            required: true,
            validate: function ($value) {
                $validator = Validator::make(['email' => $value], [
                    'email' => 'required|email|unique:admin_users,email',
                ]);

                if ($validator->fails()) {
                    return $validator->errors()->first('email');
                }

                return null;
            }
        );

        // Prompt for password
        $password = password(
            label: 'Password',
            placeholder: 'Minimum 8 characters',
            required: true,
            validate: function ($value) {
                if (strlen($value) < 8) {
                    return 'Password must be at least 8 characters.';
                }

                return null;
            }
        );

        // Prompt for phone (optional)
        $phone = text(
            label: 'Phone (optional)',
            placeholder: '+1234567890',
            required: false
        );

        // Prompt for role
        $role = select(
            label: 'Role',
            options: [
                'super_admin' => 'Super Admin (Full Access)',
                'admin' => 'Admin',
                'operator' => 'Operator',
            ],
            default: 'admin'
        );

        // Create the admin user
        try {
            $adminUser = AdminUser::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'phone' => $phone ?: null,
                'role' => $role,
                'email_verified_at' => now(),
            ]);

            $this->newLine();
            $this->info('Admin user created successfully!');
            $this->newLine();
            $this->table(
                ['Field', 'Value'],
                [
                    ['ID', $adminUser->id],
                    ['Name', $adminUser->name],
                    ['Email', $adminUser->email],
                    ['Role', $adminUser->role],
                    ['Phone', $adminUser->phone ?: 'N/A'],
                ]
            );

            $this->newLine();
            $this->comment('You can now log in to the admin panel at: '.url('/admin'));

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to create admin user: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
