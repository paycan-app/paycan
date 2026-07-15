@extends('install.layout')

@section('title', 'Database - PayCan Installer')
@section('subtitle', 'Configure your database connection')

@section('content')
<form method="POST" action="{{ route('install.database.store') }}" id="database-form">
    @csrf
    
    <div class="mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Database Configuration</h3>
        
        <!-- Database Type -->
        <div class="mb-4">
            <label class="text-sm font-medium text-gray-700 mb-2 block">Database Type</label>
            <div class="grid grid-cols-1 gap-3">
                <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none">
                    <input type="radio" name="db_connection" value="sqlite" class="sr-only" checked>
                    <span class="flex flex-1">
                        <span class="flex flex-col">
                            <span class="block text-sm font-medium text-gray-900">SQLite (Recommended)</span>
                            <span class="mt-1 flex items-center text-sm text-gray-500">
                                Perfect for getting started quickly. No additional setup required.
                            </span>
                        </span>
                    </span>
                    <svg class="h-5 w-5 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </label>
                
                <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none">
                    <input type="radio" name="db_connection" value="mysql" class="sr-only">
                    <span class="flex flex-1">
                        <span class="flex flex-col">
                            <span class="block text-sm font-medium text-gray-900">MySQL</span>
                            <span class="mt-1 flex items-center text-sm text-gray-500">
                                For production environments and high-traffic applications.
                            </span>
                        </span>
                    </span>
                    <svg class="h-5 w-5 text-primary-600 hidden" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </label>
                
                <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none">
                    <input type="radio" name="db_connection" value="mariadb" class="sr-only">
                    <span class="flex flex-1">
                        <span class="flex flex-col">
                            <span class="block text-sm font-medium text-gray-900">MariaDB</span>
                            <span class="mt-1 flex items-center text-sm text-gray-500">
                                MySQL-compatible with enhanced performance and features.
                            </span>
                        </span>
                    </span>
                    <svg class="h-5 w-5 text-primary-600 hidden" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </label>
                
                <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none">
                    <input type="radio" name="db_connection" value="pgsql" class="sr-only">
                    <span class="flex flex-1">
                        <span class="flex flex-col">
                            <span class="block text-sm font-medium text-gray-900">PostgreSQL</span>
                            <span class="mt-1 flex items-center text-sm text-gray-500">
                                Advanced features and excellent performance for complex queries.
                            </span>
                        </span>
                    </span>
                    <svg class="h-5 w-5 text-primary-600 hidden" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </label>
            </div>
        </div>

        <!-- SQLite Configuration -->
        <div id="sqlite-config" class="space-y-4">
            <div>
                <label for="sqlite_database" class="block text-sm font-medium text-gray-700 mb-2">Database File</label>
                <input type="text" name="db_database" id="sqlite_database" value="database.sqlite" 
                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 text-sm bg-gray-50 focus:bg-white">
                <p class="mt-2 text-sm text-gray-500">The SQLite database file will be created automatically.</p>
            </div>
        </div>

        <!-- MySQL/MariaDB/PostgreSQL Configuration -->
        <div id="server-config" class="space-y-4 hidden">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="db_host" class="block text-sm font-medium text-gray-700 mb-2">Host</label>
                    <input type="text" name="db_host" id="db_host" value="127.0.0.1" 
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 text-sm bg-gray-50 focus:bg-white">
                </div>
                <div>
                    <label for="db_port" class="block text-sm font-medium text-gray-700 mb-2">Port</label>
                    <input type="text" name="db_port" id="db_port" value="3306" 
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 text-sm bg-gray-50 focus:bg-white">
                </div>
            </div>
            
            <div>
                <label for="server_database" class="block text-sm font-medium text-gray-700 mb-2">Database Name</label>
                <input type="text" name="db_database" id="server_database" value="paycan" 
                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 text-sm bg-gray-50 focus:bg-white">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="db_username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                    <input type="text" name="db_username" id="db_username" value="root" 
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 text-sm bg-gray-50 focus:bg-white">
                </div>
                <div>
                    <label for="db_password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="db_password" id="db_password" 
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 text-sm bg-gray-50 focus:bg-white">
                </div>
            </div>
        </div>
    </div>

    <!-- Redis Cache Configuration -->
    <div class="bg-gray-50 rounded-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-medium text-gray-900">Redis Cache (Optional)</h3>
                <p class="text-sm text-gray-600">Configure Redis for improved caching performance</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" id="redis-toggle" class="sr-only peer" onchange="toggleRedisConfig()">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
            </label>
        </div>

        <div id="redis-config" class="space-y-4 hidden">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="redis_host" class="block text-sm font-medium text-gray-700 mb-2">Redis Host</label>
                    <input type="text" name="redis_host" id="redis_host" value="127.0.0.1" 
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 text-sm bg-gray-50 focus:bg-white">
                </div>
                <div>
                    <label for="redis_port" class="block text-sm font-medium text-gray-700 mb-2">Redis Port</label>
                    <input type="text" name="redis_port" id="redis_port" value="6379" 
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 text-sm bg-gray-50 focus:bg-white">
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="redis_password" class="block text-sm font-medium text-gray-700 mb-2">Redis Password</label>
                    <input type="password" name="redis_password" id="redis_password" 
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 text-sm bg-gray-50 focus:bg-white">
                    <p class="mt-1 text-sm text-gray-500">Leave empty if no password is required</p>
                </div>
                <div>
                    <label for="redis_database" class="block text-sm font-medium text-gray-700 mb-2">Redis Database</label>
                    <input type="number" name="redis_database" id="redis_database" value="0" min="0" max="15"
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 text-sm bg-gray-50 focus:bg-white">
                </div>
            </div>
        </div>
    </div>

    <!-- Test Connection Result -->
    <div id="test-result" class="mb-4 hidden"></div>

    <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
        <a href="{{ route('install.requirements') }}" 
           class="flex justify-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200 sm:flex-1">
            Back
        </a>
        
        <button type="button" id="test-connection-btn"
                class="flex justify-center items-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200 sm:flex-1">
            <span class="test-btn-text">Test First</span>
            <svg class="animate-spin ml-2 h-4 w-4 text-gray-700 hidden test-btn-spinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </button>
        
        <button type="submit" id="continue-btn" disabled
                class="flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gray-400 cursor-not-allowed transition-colors duration-200 sm:flex-1">
            Continue
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const radioButtons = document.querySelectorAll('input[name="db_connection"]');
    const sqliteConfig = document.getElementById('sqlite-config');
    const serverConfig = document.getElementById('server-config');
    const portInput = document.getElementById('db_port');
    
    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            // Update visual selection
            radioButtons.forEach(r => {
                const svg = r.parentElement.querySelector('svg');
                if (r.checked) {
                    svg.classList.remove('hidden');
                    r.parentElement.classList.add('ring-2', 'ring-primary-500');
                } else {
                    svg.classList.add('hidden');
                    r.parentElement.classList.remove('ring-2', 'ring-primary-500');
                }
            });
            
            // Show/hide configuration sections
            if (this.value === 'sqlite') {
                sqliteConfig.classList.remove('hidden');
                serverConfig.classList.add('hidden');
            } else {
                sqliteConfig.classList.add('hidden');
                serverConfig.classList.remove('hidden');
                
                // Set default port based on database type
                if (this.value === 'mysql' || this.value === 'mariadb') {
                    portInput.value = '3306';
                } else if (this.value === 'pgsql') {
                    portInput.value = '5432';
                }
            }
            
            // Disable continue button when configuration changes
            const continueBtn = document.getElementById('continue-btn');
            const testResult = document.getElementById('test-result');
            if (continueBtn) {
                continueBtn.disabled = true;
                continueBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
                continueBtn.classList.remove('bg-primary-600', 'hover:bg-primary-700');
            }
            if (testResult) {
                testResult.classList.add('hidden');
            }
        });
    });
    
    // Trigger initial state
    const checkedRadio = document.querySelector('input[name="db_connection"]:checked');
    if (checkedRadio) {
        checkedRadio.dispatchEvent(new Event('change'));
    }
});

// Redis toggle function
function toggleRedisConfig() {
    const toggle = document.getElementById('redis-toggle');
    const config = document.getElementById('redis-config');
    
    if (toggle.checked) {
        config.classList.remove('hidden');
    } else {
        config.classList.add('hidden');
    }
}

function disableContinueButton() {
            const continueBtn = document.getElementById('continue-btn');
            continueBtn.disabled = true;
            continueBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
            continueBtn.classList.remove('bg-primary-600', 'hover:bg-primary-700');
            
            // Clear any existing test results
            const testResult = document.getElementById('test-result');
            testResult.innerHTML = '';
            testResult.className = '';
        }
        
        // Add form submission validation
        document.getElementById('database-form').addEventListener('submit', function(e) {
            const continueBtn = document.getElementById('continue-btn');
            
            if (continueBtn.disabled) {
                e.preventDefault();
                alert('Please test your database connection before continuing.');
                return false;
            }
        });

document.addEventListener('DOMContentLoaded', function() {
    
    // Check if there are validation errors and disable continue button
    @if ($errors->any())
        disableContinueButton();
    @endif
    
    // Add event listeners to disable continue button when configuration changes
    const configFields = [
        'db_host', 'db_port', 'db_database', 'db_username', 'db_password',
        'redis_host', 'redis_port', 'redis_password', 'redis_database'
    ];
    
    configFields.forEach(fieldName => {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field) {
            field.addEventListener('change', disableContinueButton);
            field.addEventListener('input', disableContinueButton);
        }
    });
    
    // Also listen for database type changes
    document.querySelectorAll('input[name="db_connection"]').forEach(radio => {
        radio.addEventListener('change', disableContinueButton);
    });
    
    // Listen for Redis toggle changes
    const redisToggle = document.getElementById('redis-toggle');
    if (redisToggle) {
        redisToggle.addEventListener('change', disableContinueButton);
    }
    
    // Test Connection functionality
    const testBtn = document.getElementById('test-connection-btn');
    const testResult = document.getElementById('test-result');
    const testBtnText = document.querySelector('.test-btn-text');
    const testBtnSpinner = document.querySelector('.test-btn-spinner');
    
    testBtn.addEventListener('click', function() {
        // Show loading state
        testBtn.disabled = true;
        testBtnText.textContent = 'Testing...';
        testBtnSpinner.classList.remove('hidden');
        testResult.classList.add('hidden');
        
        // Collect form data
        const formData = new FormData();
        const tokenInput = document.querySelector('input[name="_token"]');
        const connectionInput = document.querySelector('input[name="db_connection"]:checked');
        
        if (!tokenInput || !connectionInput) {
            console.error('Required form elements not found');
            return;
        }
        
        formData.append('_token', tokenInput.value);
        formData.append('db_connection', connectionInput.value);
        
        const connection = connectionInput.value;
        if (connection !== 'sqlite') {
            const hostInput = document.getElementById('db_host');
            const portInput = document.getElementById('db_port');
            const usernameInput = document.getElementById('db_username');
            const passwordInput = document.getElementById('db_password');
            const databaseInput = document.getElementById('server_database');
            
            if (!hostInput || !portInput || !usernameInput || !passwordInput || !databaseInput) {
                console.error('Server database form elements not found');
                return;
            }
            
            formData.append('db_host', hostInput.value);
            formData.append('db_port', portInput.value);
            formData.append('db_username', usernameInput.value);
            formData.append('db_password', passwordInput.value);
            formData.append('db_database', databaseInput.value);
        } else {
            const sqliteInput = document.getElementById('sqlite_database');
            
            if (!sqliteInput) {
                console.error('SQLite database form element not found');
                return;
            }
            
            formData.append('db_database', sqliteInput.value);
        }
        
        // Add Redis configuration if enabled
        const redisToggle = document.getElementById('redis-toggle');
        if (redisToggle && redisToggle.checked) {
            const redisHost = document.getElementById('redis_host');
            const redisPort = document.getElementById('redis_port');
            const redisPassword = document.getElementById('redis_password');
            const redisDatabase = document.getElementById('redis_database');
            
            formData.append('redis_enabled', '1');
            formData.append('redis_host', redisHost ? redisHost.value : '127.0.0.1');
            formData.append('redis_port', redisPort ? redisPort.value : '6379');
            formData.append('redis_password', redisPassword ? redisPassword.value : '');
            formData.append('redis_database', redisDatabase ? redisDatabase.value : '0');
        }
        
        // Set up timeout handling
        const controller = new AbortController();

        const timeoutId = setTimeout(() => {
            controller.abort();
        }, 15000); // 15 second timeout
        
        // Make AJAX request
        fetch('{{ route("install.database.test") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            signal: controller.signal
        })
        .then(response => {
            clearTimeout(timeoutId); // Clear timeout on successful response
            return response.json();
        })
        .then(data => {
            // Reset button state
            testBtn.disabled = false;
            testBtnText.textContent = 'Test First';
            testBtnSpinner.classList.add('hidden');
            
            // Show result
            testResult.classList.remove('hidden');
            const continueBtn = document.getElementById('continue-btn');
            
            if (data.success) {
                testResult.className = 'mb-4 bg-green-50 border border-green-200 rounded-md p-4';
                testResult.innerHTML = `
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">${data.message}</p>
                        </div>
                    </div>
                `;
                
                // Enable continue button
                continueBtn.disabled = false;
                continueBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                continueBtn.classList.add('bg-primary-600', 'hover:bg-primary-700');
            } else {
                testResult.className = 'mb-4 bg-red-50 border border-red-200 rounded-md p-4';
                testResult.innerHTML = `
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 001.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">${data.message}</p>
                        </div>
                    </div>
                `;
                
                // Disable continue button on error
                continueBtn.disabled = true;
                continueBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
                continueBtn.classList.remove('bg-primary-600', 'hover:bg-primary-700');
            }
        })
        .catch(error => {
            clearTimeout(timeoutId); // Clear timeout on error
            
            // Reset button state
            testBtn.disabled = false;
            testBtnText.textContent = 'Test First';
            testBtnSpinner.classList.add('hidden');
            
            // Determine error message
            let errorMessage = 'Connection test failed. Please check your database configuration.';
            if (error.name === 'AbortError') {
                errorMessage = 'Connection test timed out. The database server may be unreachable or taking too long to respond.';
            } else if (error.message.includes('NetworkError') || error.message.includes('Failed to fetch')) {
                errorMessage = 'Network error occurred. Please check your internet connection and try again.';
            }
            
            // Show error
            testResult.classList.remove('hidden');
            testResult.className = 'mb-4 bg-red-50 border border-red-200 rounded-md p-4';
            testResult.innerHTML = `
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 001.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">${errorMessage}</p>
                    </div>
                </div>
            `;
        });
    });
});
</script>
@endsection

@section('progress')
<a href="{{ route('install.welcome') }}" class="flex items-center space-x-2 opacity-75 hover:opacity-100 transition-opacity cursor-pointer">
    <div class="h-2 w-2 bg-white rounded-full"></div>
    <span>Welcome</span>
</a>
<a href="{{ route('install.requirements') }}" class="flex items-center space-x-2 opacity-75 hover:opacity-100 transition-opacity cursor-pointer">
    <div class="h-2 w-2 bg-white rounded-full"></div>
    <span>Requirements</span>
</a>
<div class="flex items-center space-x-2">
    <div class="h-2 w-2 bg-white rounded-full"></div>
    <span class="font-medium">Database</span>
</div>
<div class="flex items-center space-x-2 opacity-50 cursor-not-allowed">
    <div class="h-2 w-2 bg-white rounded-full"></div>
    <span>Admin</span>
</div>
@endsection