<?php

namespace App\Http\Controllers\Install;

use App\Helpers\InstallationHelper;
use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\User;
use App\Services\Settings\SettingsManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class InstallController extends Controller
{
    /**
     * Show the welcome page
     */
    public function welcome()
    {
        // Redirect to admin if already installed
        if (InstallationHelper::isInstalled()) {
            return redirect('/admin');
        }

        return view('install.welcome');
    }

    /**
     * Show the requirements check page
     */
    public function requirements()
    {
        $checks = $this->checkRequirements();

        return view('install.requirements', compact('checks'));
    }

    /**
     * Show the database configuration page
     */
    public function database()
    {
        return view('install.database');
    }

    /**
     * Store database configuration
     */
    public function storeDatabase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'db_connection' => 'required|in:sqlite,mysql,mariadb,pgsql',
            'db_host' => 'required_unless:db_connection,sqlite',
            'db_port' => 'required_unless:db_connection,sqlite',
            'db_database' => 'required',
            'db_username' => 'required_unless:db_connection,sqlite',
            'db_password' => 'nullable',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Test database connection
        try {
            $this->testDatabaseConnection($request->all());
        } catch (\Exception $e) {
            $errorMessage = $this->getFriendlyDatabaseError($e, $request->db_connection);

            return back()->withErrors(['database' => $errorMessage])->withInput();
        }

        // Update .env file
        $this->updateEnvFile([
            'DB_CONNECTION' => $request->db_connection,
            'DB_HOST' => $request->db_host ?? '',
            'DB_PORT' => $request->db_port ?? '',
            'DB_DATABASE' => $request->db_database,
            'DB_USERNAME' => $request->db_username ?? '',
            'DB_PASSWORD' => $request->db_password ?? '',
        ]);

        // Run migrations
        try {
            Artisan::call('migrate', ['--force' => true]);
        } catch (\Exception $e) {
            $errorMessage = $this->getFriendlyMigrationError($e);

            return back()->withErrors(['migration' => $errorMessage])->withInput();
        }

        return redirect()->route('install.admin');
    }

    /**
     * Show the admin setup page
     */
    public function admin()
    {
        return view('install.admin');
    }

    /**
     * Store admin configuration
     */
    public function storeAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admin_users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Store app settings using SettingsManager
        $settingsManager = app(SettingsManager::class);
        $settingsManager->set('app.name', $request->app_name, 'string', true);
        $settingsManager->set('app.url', $request->app_url, 'string', true);

        // Also update .env file for immediate effect
        $this->updateEnvFile([
            'APP_NAME' => '"'.$request->app_name.'"',
            'APP_URL' => $request->app_url,
        ]);

        // Create admin user
        AdminUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
            'role' => 'super_admin',
        ]);

        // Mark as installed
        InstallationHelper::markAsInstalled();

        // Clear caches
        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        return redirect()->route('install.complete');
    }

    /**
     * Show the completion page
     */
    public function complete()
    {
        if (! InstallationHelper::isInstalled()) {
            return redirect()->route('install.welcome');
        }

        return view('install.complete');
    }

    /**
     * Check system requirements
     */
    private function checkRequirements(): array
    {
        return [
            'php_version' => [
                'required' => '8.2',
                'current' => PHP_VERSION,
                'ok' => version_compare(PHP_VERSION, '8.2.0', '>='),
            ],
            'extensions' => [
                'bcmath' => extension_loaded('bcmath'),
                'ctype' => extension_loaded('ctype'),
                'curl' => extension_loaded('curl'),
                'dom' => extension_loaded('dom'),
                'fileinfo' => extension_loaded('fileinfo'),
                'json' => extension_loaded('json'),
                'mbstring' => extension_loaded('mbstring'),
                'openssl' => extension_loaded('openssl'),
                'pcre' => extension_loaded('pcre'),
                'pdo' => extension_loaded('pdo'),
                'tokenizer' => extension_loaded('tokenizer'),
                'xml' => extension_loaded('xml'),
                'gmp' => extension_loaded('gmp'),
            ],
            'write_permissions' => [
                'storage' => is_writable(storage_path()),
                'bootstrap_cache' => is_writable(base_path('bootstrap/cache')),
                'database' => is_writable(database_path()),
            ],
        ];
    }

    /**
     * Update .env file
     */
    private function updateEnvFile(array $data): void
    {
        $envFile = base_path('.env');
        $envContent = File::exists($envFile) ? File::get($envFile) : '';

        foreach ($data as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$value}";

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }

        File::put($envFile, $envContent);
    }

    /**
     * Get user-friendly database error message
     */
    private function getFriendlyDatabaseError(\Exception $e, string $connection): string
    {
        $message = $e->getMessage();

        // Connection refused errors
        if (str_contains($message, 'Connection refused') || str_contains($message, '2002')) {
            return "Cannot connect to the {$connection} server. Please check if the database server is running and the host/port are correct.";
        }

        // Access denied errors
        if (str_contains($message, 'Access denied') || str_contains($message, '1045')) {
            return 'Access denied. Please check your database username and password.';
        }

        // Unknown database errors
        if (str_contains($message, 'Unknown database') || str_contains($message, '1049')) {
            return 'The specified database does not exist. Please create the database first or check the database name.';
        }

        // Host not found errors
        if (str_contains($message, 'getaddrinfo failed') || str_contains($message, 'Name or service not known')) {
            return 'Cannot resolve the database host. Please check the hostname or IP address.';
        }

        // PostgreSQL specific errors
        if ($connection === 'pgsql') {
            if (str_contains($message, 'could not connect to server')) {
                return 'Cannot connect to PostgreSQL server. Please check if PostgreSQL is running and accessible.';
            }
            if (str_contains($message, 'database') && str_contains($message, 'does not exist')) {
                return 'The PostgreSQL database does not exist. Please create the database first.';
            }
            if (str_contains($message, 'authentication failed')) {
                return 'PostgreSQL authentication failed. Please check your username and password.';
            }
        }

        // SQLite specific errors
        if ($connection === 'sqlite') {
            if (str_contains($message, 'unable to open database file')) {
                return 'Cannot create or access the SQLite database file. Please check directory permissions.';
            }
        }

        // Generic fallback
        return 'Database connection failed. Please check your database configuration and try again.';
    }

    /**
     * Get user-friendly migration error message
     */
    private function getFriendlyMigrationError(\Exception $e): string
    {
        $message = $e->getMessage();

        // Table already exists
        if (str_contains($message, 'already exists') || str_contains($message, '1050')) {
            return 'Database tables already exist. The application may already be installed.';
        }

        // Permission errors
        if (str_contains($message, 'Access denied') || str_contains($message, 'permission')) {
            return 'Insufficient database permissions to create tables. Please ensure the database user has CREATE privileges.';
        }

        // Connection lost during migration
        if (str_contains($message, 'server has gone away') || str_contains($message, 'Lost connection')) {
            return 'Database connection was lost during migration. Please check your database server and try again.';
        }

        // Disk space issues
        if (str_contains($message, 'No space left') || str_contains($message, 'disk full')) {
            return 'Insufficient disk space to complete the migration. Please free up space and try again.';
        }

        // Generic fallback
        return 'Database migration failed. Please check your database configuration and permissions, then try again.';
    }

    /**
     * Test database connection
     */
    private function testDatabaseConnection(array $config): void
    {
        $connection = $config['db_connection'];

        if ($connection === 'sqlite') {
            $dbPath = database_path($config['db_database']);
            if (! File::exists($dbPath)) {
                File::put($dbPath, '');
            }
            $dsn = "sqlite:{$dbPath}";
            new \PDO($dsn);
        } else {
            $host = $config['db_host'];
            $port = $config['db_port'];
            $database = $config['db_database'];
            $username = $config['db_username'];
            $password = $config['db_password'] ?? '';

            if ($connection === 'mysql' || $connection === 'mariadb') {
                $dsn = "mysql:host={$host};port={$port};dbname={$database}";
            } elseif ($connection === 'pgsql') {
                $dsn = "pgsql:host={$host};port={$port};dbname={$database}";
            }

            new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);
        }
    }
}
