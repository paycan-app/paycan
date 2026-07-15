# Settings Usage Examples

This document demonstrates how to read and save settings in the PayCan application.

## Table of Contents
- [Reading Settings in Code](#reading-settings-in-code)
- [How Settings are Saved in Filament Admin](#how-settings-are-saved-in-filament-admin)
- [Database Structure](#database-structure)
- [Common Use Cases](#common-use-cases)

---

## Reading Settings in Code

### Using the Global `settings()` Helper

The easiest way to read settings is using the `settings()` helper function:

```php
// Get a single setting with dot notation
$stripeEnabled = settings('stripe.enabled', false);
$stripeApiKey = settings('stripe.api_key');
$stripePublishableKey = settings('stripe.publishable_key');

// Downloader settings
$downloaderEnabled = settings('downloader.enabled', true);
$linkExpiry = settings('downloader.link_expiry', 48);
$maxDownloads = settings('downloader.max_downloads', 5);
```

### Using the SettingsManager Service

For more advanced usage, inject the `SettingsManager` service:

```php
use App\Services\Settings\SettingsManager;

class ExampleController extends Controller
{
    public function __construct(
        protected SettingsManager $settingsManager
    ) {}

    public function processPayment()
    {
        // Get a single setting
        $stripeEnabled = $this->settingsManager->get('stripe.enabled', false);

        if ($stripeEnabled) {
            $apiKey = $this->settingsManager->get('stripe.api_key');
            // Process payment with Stripe
        }
    }

    public function getGroupSettings()
    {
        // Get all settings in a group
        $stripeSettings = $this->settingsManager->getByGroup('stripe');

        // Returns:
        // [
        //     'enabled' => true,
        //     'api_key' => 'sk_test_...',
        //     'publishable_key' => 'pk_test_...',
        //     'enable_subscriptions' => true,
        //     'webhook_secret' => 'whsec_...'
        // ]
    }
}
```

### Example: Stripe Payment Gateway

```php
use App\Services\Settings\SettingsManager;

class StripeService
{
    public function __construct(
        protected SettingsManager $settings
    ) {}

    public function initializeStripe(): ?\Stripe\StripeClient
    {
        // Check if Stripe is enabled
        if (!settings('stripe.enabled', false)) {
            return null;
        }

        // Get the API key (automatically decrypted if encrypted)
        $apiKey = settings('stripe.api_key');

        // Initialize Stripe
        return new \Stripe\StripeClient($apiKey);
    }

    public function createPaymentIntent(int $amount): array
    {
        $stripe = $this->initializeStripe();

        if (!$stripe) {
            throw new \Exception('Stripe is not enabled');
        }

        return $stripe->paymentIntents->create([
            'amount' => $amount,
            'currency' => 'usd',
        ]);
    }

    public function supportsSubscriptions(): bool
    {
        return (bool) settings('stripe.enable_subscriptions', true);
    }
}
```

### Example: Download Link Generator

```php
use App\Services\Settings\SettingsManager;
use Carbon\Carbon;

class DownloadLinkService
{
    public function generateDownloadLink(Order $order, Product $product): ?string
    {
        // Check if downloader is enabled
        if (!settings('downloader.enabled', true)) {
            return null;
        }

        // Get settings
        $expiryHours = settings('downloader.link_expiry', 48);
        $maxDownloads = settings('downloader.max_downloads', 5);

        // Generate secure token
        $token = Str::random(64);

        // Store download link in database
        $downloadLink = DownloadLink::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'token' => $token,
            'expires_at' => Carbon::now()->addHours($expiryHours),
            'max_downloads' => $maxDownloads,
            'downloads_count' => 0,
        ]);

        return route('download.file', ['token' => $token]);
    }

    public function validateDownload(string $token): bool
    {
        $link = DownloadLink::where('token', $token)->first();

        if (!$link) {
            return false;
        }

        // Check expiry
        if ($link->expires_at < now()) {
            return false;
        }

        // Check max downloads
        $maxDownloads = settings('downloader.max_downloads', 5);
        if ($link->downloads_count >= $maxDownloads) {
            return false;
        }

        return true;
    }
}
```

---

## How Settings are Saved in Filament Admin

### 1. Settings Provider Schema

Settings providers define the form schema with field names using `__` (double underscore):

```php
// app/Services/Payment/Gateways/StripeSettingsProvider.php
public function getSchema(): Schema
{
    return Schema::make()
        ->components([
            Section::make('Stripe Configuration')
                ->schema([
                    // Field name uses __ pattern
                    Toggle::make('stripe__enabled')
                        ->label('Enable Stripe')
                        ->default(false),

                    TextInput::make('stripe__api_key')
                        ->label('Secret Key')
                        ->password()->revealable()
                        ->required(),

                    TextInput::make('stripe__publishable_key')
                        ->label('Publishable Key')
                        ->required(),

                    Toggle::make('stripe__enable_subscriptions')
                        ->label('Enable Subscription Support')
                        ->default(true),

                    TextInput::make('stripe__webhook_secret')
                        ->label('Webhook Secret')
                        ->password()->revealable(),
                ])
        ]);
}

// Default values (keys without group prefix)
public function getDefaults(): array
{
    return [
        'enabled' => false,
        'enable_subscriptions' => true,
        'api_key' => '',
        'publishable_key' => '',
        'webhook_secret' => '',
    ];
}
```

### 2. Loading Settings into the Form

The settings page loads settings from the database and converts them to form field names:

```php
// app/Filament/Pages/Settings/PaymentProvidersSettings.php
protected function loadSettings(): void
{
    $manager = app(SettingsManager::class);
    $this->data = [];

    foreach ($this->providers as $provider) {
        $group = $provider->getGroup(); // 'stripe'
        $settings = $manager->getByGroup($group); // Get from database
        $defaults = $provider->getDefaults();

        $settings = array_merge($defaults, $settings);

        foreach ($settings as $key => $value) {
            // Convert: stripe.api_key → stripe__api_key
            $fieldName = str_replace('.', '__', "{$group}__{$key}");
            $this->data[$fieldName] = $value;

            // Example:
            // 'stripe__enabled' => true
            // 'stripe__api_key' => 'sk_test_...'
            // 'stripe__publishable_key' => 'pk_test_...'
        }
    }
}
```

### 3. Saving Settings from the Form

When the admin clicks "Save", the form data is converted back to dot notation and saved:

```php
// app/Filament/Pages/Settings/PaymentProvidersSettings.php
public function save(): void
{
    $data = $this->form->getState();
    $manager = app(SettingsManager::class);

    foreach ($this->providers as $provider) {
        $group = $provider->getGroup(); // 'stripe'
        $defaults = $provider->getDefaults();

        foreach (array_keys($defaults) as $key) {
            // Convert form field name back to dot notation
            // stripe__api_key → get from form data
            $fieldName = str_replace('.', '__', "{$group}__{$key}");
            $value = data_get($data, $fieldName);

            // Determine type and visibility
            $type = $this->inferType($key, $value);
            $isPublic = !in_array($key, ['api_key', 'webhook_secret', 'client_secret']);

            // Save to database with dot notation: stripe.api_key
            $manager->set("{$group}.{$key}", $value, $type, $isPublic);
        }
    }

    $manager->clearCache();
}

private function inferType(string $key, mixed $value): string
{
    // Automatically encrypt sensitive keys
    if (str_contains($key, 'secret') || str_contains($key, 'api_key') || str_contains($key, 'password')) {
        return 'encrypted';
    }

    return match (gettype($value)) {
        'boolean' => 'boolean',
        'integer' => 'integer',
        'array' => 'array',
        default => 'string',
    };
}
```

---

## Database Structure

Settings are stored in the `settings` table:

```sql
CREATE TABLE settings (
    id BIGINT PRIMARY KEY,
    key VARCHAR(255) UNIQUE,     -- e.g., 'stripe.api_key'
    value TEXT,                  -- Stored value (encrypted if type='encrypted')
    type VARCHAR(50),            -- 'string', 'boolean', 'integer', 'array', 'encrypted'
    is_public BOOLEAN,           -- Whether this setting is public or private
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Example Database Records

```
| key                              | value                 | type      | is_public |
|----------------------------------|-----------------------|-----------|-----------|
| stripe.enabled                   | 1                     | boolean   | true      |
| stripe.api_key                   | [encrypted]           | encrypted | false     |
| stripe.publishable_key           | pk_test_...           | string    | true      |
| stripe.enable_subscriptions      | 1                     | boolean   | true      |
| stripe.webhook_secret            | [encrypted]           | encrypted | false     |
| downloader.enabled | 1          | boolean   | true      |
| downloader.link_expiry | 48     | integer   | true      |
| downloader.max_downloads | 5    | integer   | true      |
```

---

## Common Use Cases

### Use Case 1: Check if Payment Gateway is Enabled

```php
// In a controller or service
public function showCheckout()
{
    $availableGateways = [];

    if (settings('stripe.enabled', false)) {
        $availableGateways[] = [
            'name' => 'Stripe',
            'supports_subscriptions' => settings('stripe.enable_subscriptions', true),
        ];
    }

    if (settings('paypal.enabled', false)) {
        $availableGateways[] = [
            'name' => 'PayPal',
            'supports_subscriptions' => settings('paypal.enable_subscriptions', true),
        ];
    }

    return view('checkout', compact('availableGateways'));
}
```

### Use Case 2: Generate License Keys

```php
use App\Services\Settings\SettingsManager;

class LicenseGenerator
{
    public function generate(Order $order): ?string
    {
        // Check if license generator is enabled
        if (!settings('license_generator.enabled', true)) {
            return null;
        }

        // Get settings
        $length = settings('license_generator.key_length', 16);
        $prefix = settings('license_generator.prefix', '');

        // Generate license key
        $key = strtoupper(Str::random($length));

        // Add prefix if set
        if ($prefix) {
            $key = $prefix . $key;
        }

        return $key;
    }
}
```

### Use Case 3: Dynamic Configuration

```php
// In a service provider or middleware
use App\Services\Settings\SettingsManager;

class ConfigureMailSettings
{
    public function handle($request, Closure $next)
    {
        // Dynamically configure mail settings from database
        config([
            'mail.default' => settings('mail.mailer', 'smtp'),
            'mail.from.address' => settings('mail.from_address'),
            'mail.from.name' => settings('mail.from_name'),
            'mail.mailers.smtp.host' => settings('mail.host'),
            'mail.mailers.smtp.port' => settings('mail.port'),
            'mail.mailers.smtp.encryption' => settings('mail.encryption'),
            'mail.mailers.smtp.username' => settings('mail.username'),
            'mail.mailers.smtp.password' => settings('mail.password'),
        ]);

        return $next($request);
    }
}
```

### Use Case 4: Webhooks

```php
class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Get webhook secret from settings
        $webhookSecret = settings('stripe.webhook_secret');

        if (!$webhookSecret) {
            return response()->json(['error' => 'Webhook not configured'], 400);
        }

        try {
            // Verify webhook signature
            $event = \Stripe\Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature'),
                $webhookSecret
            );

            // Process webhook event
            $this->processWebhookEvent($event);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
```

---

## Best Practices

### 1. Always Provide Defaults

```php
// Good: Provides fallback value
$enabled = settings('stripe.enabled', false);

// Bad: Could return null unexpectedly
$enabled = settings('stripe.enabled');
```

### 2. Cache Settings in Performance-Critical Code

```php
class HighTrafficController extends Controller
{
    protected array $gatewaySettings;

    public function __construct()
    {
        // Cache settings once in constructor
        $this->gatewaySettings = [
            'stripe_enabled' => settings('stripe.enabled', false),
            'paypal_enabled' => settings('paypal.enabled', false),
        ];
    }

    public function process()
    {
        // Use cached settings
        if ($this->gatewaySettings['stripe_enabled']) {
            // Process...
        }
    }
}
```

### 3. Use Type Casting

```php
// Boolean settings
$enabled = (bool) settings('stripe.enabled', false);

// Integer settings
$maxDownloads = (int) settings('downloader.max_downloads', 5);

// Array settings
$allowedCountries = (array) settings('payment.allowed_countries', []);
```

### 4. Check Settings Before Using Sensitive Data

```php
public function initializeGateway()
{
    if (!settings('stripe.enabled', false)) {
        throw new \Exception('Stripe gateway is not enabled');
    }

    $apiKey = settings('stripe.api_key');

    if (!$apiKey) {
        throw new \Exception('Stripe API key is not configured');
    }

    return new StripeClient($apiKey);
}
```

---

## Summary

### Key Points:

1. **Reading Settings**: Use `settings('key.name', 'default')` helper
2. **Form Field Names**: Use `__` (double underscore) → `stripe__api_key`
3. **Database Keys**: Use `.` (dot notation) → `stripe.api_key`
4. **Encryption**: Sensitive fields (api_key, secret, password) are automatically encrypted
5. **Public/Private**: Settings with sensitive names are marked as private automatically

### Pattern Flow:

```
Admin Form          Database           Code Usage
-----------         ---------          ----------
stripe__enabled  →  stripe.enabled  →  settings('stripe.enabled')
stripe__api_key  →  stripe.api_key  →  settings('stripe.api_key')
```
