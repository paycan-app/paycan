<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Exception;

class InstallController extends Controller
{
    public function welcome()
    {
        if ($this->isInstalled()) {
            return redirect('/admin');
        }

        return view('install.welcome');
    }

    public function requirements()
    {
        if ($this->isInstalled()) {
            return redirect('/admin');
        }

        $checks = $this->checkRequirements();
        
        return view('install.requirements', compact('checks'));
    }

    public function database()
    {
        if ($this->isInstalled()) {
            return redirect('/admin');
        }

        return view('install.database');
    }

    public function testDatabase(Request $request)
    {
        $rules = [
            'db_connection' => 'required|in:sqlite,mysql,mariadb,pgsql',
            'db_host' => 'required_unless:db_connection,sqlite',
            'db_port' => 'required_unless:db_connection,sqlite|numeric',
            'db_database' => 'required',
            'db_username' => 'required_unless:db_connection,sqlite',
            'db_password' => 'nullable',
        ];

        // Add Redis validation if enabled
        if ($request->has('redis_enabled')) {
            $rules['redis_host'] = 'required|string';
            $rules['redis_port'] = 'required|numeric';
            $rules['redis_password'] = 'nullable|string';
            $rules['redis_database'] = 'required|numeric|min:0|max:15';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please fill in all required fields.',
                'errors' => $validator->errors()
            ], 422);
        }

        $results = [];
        $allSuccessful = true;

        try {
            // Test database connection
            $this->testDatabaseConnection($request->all());
            $results[] = 'Database connection successful!';
        } catch (Exception $e) {
            $allSuccessful = false;
            $errorMessage = $this->getFriendlyDatabaseError($e, $request->db_connection);
            $results[] = 'Database: ' . $errorMessage;
        }

        // Test Redis connection if enabled
        if ($request->has('redis_enabled')) {
            try {
                $this->testRedisConnection($request->all());
                $results[] = 'Redis connection successful!';
            } catch (Exception $e) {
                $allSuccessful = false;
                $results[] = 'Redis: ' . $e->getMessage();
            }
        }

        if ($allSuccessful) {
            return response()->json([
                'success' => true,
                'message' => implode(' ', $results)
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => implode('<br/>', $results)
            ], 400);
        }
    }

    public function databaseStore(Request $request)
    {
        $rules = [
            'db_connection' => 'required|in:sqlite,mysql,mariadb,pgsql',
            'db_host' => 'required_unless:db_connection,sqlite',
            'db_port' => 'required_unless:db_connection,sqlite|numeric',
            'db_database' => 'required',
            'db_username' => 'required_unless:db_connection,sqlite',
            'db_password' => 'nullable',
        ];

        // Add Redis validation if enabled
        if ($request->has('redis_enabled')) {
            $rules['redis_host'] = 'required|string';
            $rules['redis_port'] = 'required|numeric';
            $rules['redis_password'] = 'nullable|string';
            $rules['redis_database'] = 'required|numeric|min:0|max:15';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $this->updateEnvFile($request->all());
            $this->testDatabaseConnection($request->all());
            
            // Test Redis connection if enabled
            if ($request->has('redis_enabled')) {
                $this->testRedisConnection($request->all());
            }
            
            return redirect()->route('install.admin');
        } catch (Exception $e) {
            $errorMessage = $this->getFriendlyDatabaseError($e, $request->db_connection);
            return back()->withErrors(['database' => $errorMessage])->withInput();
        }
    }

    public function admin()
    {
        if ($this->isInstalled()) {
            return redirect('/admin');
        }

        return view('install.admin');
    }

    public function adminStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Update app configuration
            $this->updateEnvFile([
                'APP_NAME' => $request->app_name,
                'APP_URL' => $request->app_url,
            ]);

            // Run migrations
            Artisan::call('migrate', ['--force' => true]);

            // Create admin user
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
            ]);

            // Mark as installed
            $this->markAsInstalled();

            return redirect()->route('install.complete');
        } catch (Exception $e) {
            return back()->withErrors(['installation' => 'Installation failed: ' . $e->getMessage()])->withInput();
        }
    }

    public function complete()
    {
        if (!$this->isInstalled()) {
            return redirect()->route('install.welcome');
        }

        return view('install.complete');
    }

    private function checkRequirements()
    {
        return [
            'php_version' => [
                'required' => '8.2.0',
                'current' => PHP_VERSION,
                'ok' => version_compare(PHP_VERSION, '8.2.0', '>=')
            ],
            'extensions' => [
                'bcmath' => extension_loaded('bcmath'),
                'ctype' => extension_loaded('ctype'),
                'curl' => extension_loaded('curl'),
                'dom' => extension_loaded('dom'),
                'fileinfo' => extension_loaded('fileinfo'),
                'filter' => extension_loaded('filter'),
                'hash' => extension_loaded('hash'),
                'mbstring' => extension_loaded('mbstring'),
                'openssl' => extension_loaded('openssl'),
                'pcre' => extension_loaded('pcre'),
                'pdo' => extension_loaded('pdo'),
                'session' => extension_loaded('session'),
                'tokenizer' => extension_loaded('tokenizer'),
                'xml' => extension_loaded('xml'),
                'zip' => extension_loaded('zip'),
                'sqlite3' => extension_loaded('sqlite3'),
            ],
            'write_permissions' => [
                'storage' => is_writable(storage_path()),
                'bootstrap_cache' => is_writable(base_path('bootstrap/cache')),
            ],
            'composer' => $this->commandExists('composer'),
            'node' => $this->commandExists('node'),
        ];
    }

    private function commandExists($command)
    {
        $whereIsCommand = (PHP_OS == 'WINNT') ? 'where' : 'which';
        $process = proc_open(
            "$whereIsCommand $command",
            [
                0 => ["pipe", "r"], // stdin
                1 => ["pipe", "w"], // stdout
                2 => ["pipe", "w"], // stderr
            ],
            $pipes
        );
        
        if ($process !== false) {
            $stdout = stream_get_contents($pipes[1]);
            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $returnCode = proc_close($process);
            return $returnCode === 0;
        }
        
        return false;
    }

    private function updateEnvFile(array $data)
    {
        $envFile = base_path('.env');
        
        if (!File::exists($envFile)) {
            File::copy(base_path('.env.example'), $envFile);
        }

        $envContent = File::get($envFile);

        // Handle Redis configuration
        if (isset($data['redis_enabled'])) {
            $redisConfig = [
                'REDIS_HOST' => $data['redis_host'] ?? '127.0.0.1',
                'REDIS_PASSWORD' => $data['redis_password'] ?? 'null',
                'REDIS_PORT' => $data['redis_port'] ?? '6379',
                'REDIS_DB' => $data['redis_database'] ?? '0',
                'CACHE_DRIVER' => 'redis',
                'SESSION_DRIVER' => 'redis',
                'QUEUE_CONNECTION' => 'redis',
            ];
            
            foreach ($redisConfig as $key => $value) {
                $this->updateEnvVariable($envContent, $key, $value);
            }
            
            // Remove redis_enabled from data to avoid processing it as a regular env var
            unset($data['redis_enabled'], $data['redis_host'], $data['redis_port'], $data['redis_password'], $data['redis_database']);
        }

        foreach ($data as $key => $value) {
            $key = strtoupper($key);
            $value = is_null($value) ? '' : $value;
            
            $this->updateEnvVariable($envContent, $key, $value);
        }

        File::put($envFile, $envContent);
    }

    private function updateEnvVariable(&$envContent, $key, $value)
    {
        // Handle values that need quotes
        if (is_string($value) && (str_contains($value, ' ') || empty($value))) {
            $value = '"' . $value . '"';
        }
        
        if (str_contains($envContent, $key . '=')) {
            $envContent = preg_replace(
                "/^{$key}=.*/m",
                "{$key}={$value}",
                $envContent
            );
        } else {
            $envContent .= "\n{$key}={$value}";
        }
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
            return "Access denied. Please check your database username and password.";
        }
        
        // Unknown database errors
        if (str_contains($message, 'Unknown database') || str_contains($message, '1049')) {
            return "The specified database does not exist. Please create the database first or check the database name.";
        }
        
        // Host not found errors
        if (str_contains($message, 'getaddrinfo failed') || str_contains($message, 'Name or service not known')) {
            return "Cannot resolve the database host. Please check the hostname or IP address.";
        }
        
        // PostgreSQL specific errors
        if ($connection === 'pgsql') {
            if (str_contains($message, 'could not connect to server')) {
                return "Cannot connect to PostgreSQL server. Please check if PostgreSQL is running and accessible.";
            }
            if (str_contains($message, 'database') && str_contains($message, 'does not exist')) {
                return "The PostgreSQL database does not exist. Please create the database first.";
            }
            if (str_contains($message, 'authentication failed')) {
                return "PostgreSQL authentication failed. Please check your username and password.";
            }
        }
        
        // SQLite specific errors
        if ($connection === 'sqlite') {
            if (str_contains($message, 'unable to open database file')) {
                return "Cannot create or access the SQLite database file. Please check directory permissions.";
            }
        }
        
        // Generic fallback
        return "Database connection failed. Please check your database configuration and try again.";
    }

    private function testDatabaseConnection(array $config)
    {
        if ($config['db_connection'] === 'sqlite') {
            $dbPath = database_path('database.sqlite');
            if (!File::exists($dbPath)) {
                File::put($dbPath, '');
            }
            return;
        }

        $driver = $config['db_connection'];
        // MariaDB uses the MySQL driver
        if ($driver === 'mariadb') {
            $driver = 'mysql';
        }
        
        $connection = [
            'driver' => $driver,
            'host' => $config['db_host'],
            'port' => $config['db_port'],
            'database' => $config['db_database'],
            'username' => $config['db_username'],
            'password' => $config['db_password'],
            'options' => [
                \PDO::ATTR_TIMEOUT => 5, // 5 second timeout
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ],
        ];

        // Add driver-specific timeout options
        if ($driver === 'mysql') {
            if (defined('PDO::MYSQL_ATTR_INIT_COMMAND')) {
                $connection['options'][\PDO::MYSQL_ATTR_INIT_COMMAND] = "SET SESSION wait_timeout=5";
            }
            if (defined('PDO::MYSQL_ATTR_CONNECT_TIMEOUT')) {
                $connection['options'][\PDO::MYSQL_ATTR_CONNECT_TIMEOUT] = 5;
            }
        } elseif ($driver === 'pgsql') {
            if (defined('PDO::PGSQL_ATTR_DISABLE_PREPARES')) {
                $connection['options'][\PDO::PGSQL_ATTR_DISABLE_PREPARES] = true;
            }
        }

        config(['database.connections.test' => $connection]);
        
        // Set a maximum execution time for the connection test
        $originalTimeLimit = ini_get('max_execution_time');
        set_time_limit(10); // 10 seconds max
        
        try {
            DB::connection('test')->getPdo();
        } finally {
            // Restore original time limit
            set_time_limit($originalTimeLimit);
        }
    }

    private function testRedisConnection($config)
    {
        if (!extension_loaded('redis')) {
            throw new Exception('Redis PHP extension is not installed');
        }

        $redis = new \Redis();
        
        try {
            // Set connection timeout
            $redis->connect(
                $config['redis_host'] ?? '127.0.0.1',
                (int)($config['redis_port'] ?? 6379),
                5 // 5 seconds timeout
            );

            // Authenticate if password is provided
            if (!empty($config['redis_password'])) {
                if (!$redis->auth($config['redis_password'])) {
                    throw new Exception('Redis authentication failed');
                }
            }

            // Select database
            $database = (int)($config['redis_database'] ?? 0);
            if (!$redis->select($database)) {
                throw new Exception('Failed to select Redis database ' . $database);
            }

            // Test basic operations
            $testKey = 'paycan_install_test_' . time();
            $redis->set($testKey, 'test_value', 10); // 10 seconds TTL
            $value = $redis->get($testKey);
            $redis->del($testKey);

            if ($value !== 'test_value') {
                throw new Exception('Redis read/write test failed');
            }

        } catch (\RedisException $e) {
            throw new Exception('Redis connection failed: ' . $e->getMessage());
        } finally {
            if ($redis->isConnected()) {
                $redis->close();
            }
        }
    }

    private function isInstalled()
    {
        return File::exists(storage_path('installed'));
    }

    private function markAsInstalled()
    {
        File::put(storage_path('installed'), now()->toDateTimeString());
    }
}