<?php

namespace App\Console\Commands;

use App\Services\Settings\SettingsManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'paycan:install {--force : Force installation even if already installed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install PayCan application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (File::exists(storage_path('installed')) && !$this->option('force')) {
            $this->error('PayCan is already installed. Use --force to reinstall.');
            return 1;
        }

        $this->info('🚀 Installing PayCan...');

        // Create .env file if it doesn't exist
        if (!File::exists(base_path('.env'))) {
            File::copy(base_path('.env.example'), base_path('.env'));
            $this->info('✅ Created .env file');
        }

        // Generate application key
        Artisan::call('key:generate', ['--force' => true]);
        $this->info('✅ Generated application key');

        // Create SQLite database if it doesn't exist
        $dbPath = database_path('database.sqlite');
        if (!File::exists($dbPath)) {
            File::put($dbPath, '');
            $this->info('✅ Created SQLite database');
        }

        // Run migrations
        Artisan::call('migrate', ['--force' => true]);
        $this->info('✅ Database migrated');

        // Install and build frontend assets
        if ($this->confirm('Install and build frontend assets?', true)) {
            $this->info('📦 Installing npm dependencies...');
            exec('npm install');
            
            $this->info('🔨 Building assets...');
            exec('npm run build');
            $this->info('✅ Frontend assets built');
        }

        // Create storage link
        Artisan::call('storage:link');
        $this->info('✅ Storage linked');

        // Clear caches
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        $this->info('✅ Caches cleared');

        $this->info('');
        $this->info('🎉 PayCan installation completed!');
        $this->info('');
        $this->info('Next steps:');
        $this->info('1. Visit /install to complete the web-based setup');
        $this->info('2. Or create an admin user with: php artisan make:filament-user');
        $this->info('3. Access the admin panel at /admin');

        return 0;
    }
}