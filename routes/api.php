<?php

use App\Http\Controllers\Api\Admin\UserTokenController;
use App\Http\Controllers\Api\User\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PayCan API Routes
|--------------------------------------------------------------------------
| PayCan is an integration system for payment needs. External apps sync their
| users with PayCan and use our APIs to manage payments on behalf of users.
|
| Authentication Types:
| 1. JWT Token (User-scoped) - For operations on behalf of synced users
|    - Use: Authorization: Bearer <jwt_token>
|    - Get token via POST /api/admin/users/sync
|    - Allows: Create orders, manage subscriptions, view licenses, downloads
|    - Does NOT allow: Admin settings, product/price management
|
| 2. API Secret Key (Admin-scoped) - For admin operations
|    - Use: X-API-Key: <api_secret_key> header (required)
   - Query Parameter: ?api_key=<api_secret_key> (only in local development for debugging)
   - Note: Admin API does NOT support Authorization Bearer token (intentional design)
|    - Get key from Admin Panel > Settings > API Secret Key
|    - Allows: Manage products, prices, settings, view all data
|
*/

/*
|--------------------------------------------------------------------------
| Temporary Disabled -Public Auth Routes (No Authentication Required)
|--------------------------------------------------------------------------
| Optional authentication routes for traditional login/register flow
| Note: The primary integration method is Admin API sync (/api/admin/users/sync)
*/

// Temporary disabled as the authentication is done by /user/sync on admin side. We do not have a seprate login system for end-user at the moment
// Route::prefix('auth')->middleware(['throttle:60,1'])->group(function () {
// Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');
// Route::post('/register', [AuthController::class, 'register'])->name('api.auth.register');
// Route::post('/token', [AuthController::class, 'createToken'])->name('api.auth.token');
// Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('api.auth.forgot-password');
// Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('api.auth.reset-password');
// });

Route::prefix('auth')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
    Route::get('/me', [AuthController::class, 'me'])->name('api.auth.me');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('api.auth.refresh');
});

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
| Routes that can be accessed without authentication
*/

// Payment Gateways (public information)
Route::prefix('payment-gateways')->controller(\App\Http\Controllers\Api\PaymentGatewayController::class)->group(function () {
    Route::get('/', 'index')->name('api.payment-gateways.index');
    Route::get('/products/{product}', 'forProduct')->name('api.payment-gateways.for-product');
    Route::get('/product-prices/{productPrice}', 'forProductPrice')->name('api.payment-gateways.for-product-price');
    Route::post('/validate', 'validate')->name('api.payment-gateways.validate');
});

/*
|--------------------------------------------------------------------------
| User-Scoped Routes (JWT Token Required)
|--------------------------------------------------------------------------
| Operations on behalf of synced users - orders, subscriptions, downloads, licenses
| Cannot access admin settings or product/price management
*/

Route::prefix('user')->middleware(['auth:sanctum', 'throttle:120,1'])->group(function () {
    // User info (kept for backward compatibility)
    Route::get('/me', [AuthController::class, 'me'])
        ->name('api.user.me');

    // Orders
    Route::prefix('orders')->controller(\App\Http\Controllers\Api\User\OrderController::class)->group(function () {
        Route::get('/', 'index')->name('api.user.orders.index');
        Route::get('/{order}', 'show')->name('api.user.orders.show');
        Route::get('/{order}/downloads', 'downloads')->name('api.user.orders.downloads');
        Route::get('/{order}/licenses', 'licenses')->name('api.user.orders.licenses');
    });

    // Subscriptions
    Route::prefix('subscriptions')->controller(\App\Http\Controllers\Api\User\SubscriptionController::class)->group(function () {
        Route::get('/', 'index')->name('api.user.subscriptions.index');
        Route::post('/', 'store')->name('api.user.subscriptions.create');
        Route::get('/{subscription}', 'show')->name('api.user.subscriptions.show');
        Route::post('/{subscription}/cancel', 'cancel')->name('api.user.subscriptions.cancel');
        Route::post('/{subscription}/resume', 'resume')->name('api.user.subscriptions.resume');
        Route::post('/{subscription}/change', 'change')->name('api.user.subscriptions.change');
    });

    // Checkout & Payment
    Route::prefix('checkout')->controller(\App\Http\Controllers\Api\User\CheckoutController::class)->group(function () {
        Route::post('/portal', 'portal')->name('api.user.checkout.portal');
        // Rate limit: 10 cancellations per minute to prevent abuse
        Route::post('/{order}/cancel', 'cancel')
            ->middleware('throttle:10,1')
            ->name('api.user.checkout.cancel');
    });

    // Transactions
    Route::prefix('transactions')->controller(\App\Http\Controllers\Api\User\TransactionController::class)->group(function () {
        Route::get('/', 'index')->name('api.user.transactions.index');
        Route::get('/{transaction}', 'show')->name('api.user.transactions.show');
    });

}); // closes the 'user' group

// Products (active products with prices) - no user authentication required
Route::prefix('user/products')->controller(\App\Http\Controllers\Api\User\ProductController::class)->group(function () {
    Route::get('/', 'index')->name('api.user.products.index');
    Route::get('/{product}', 'show')->name('api.user.products.show');
}); // closes the products group

/*
|--------------------------------------------------------------------------
| Guest/Optional Auth Routes
|--------------------------------------------------------------------------
| Routes that can work with or without authentication
*/

// Checkout Preview - public endpoint for preview before checkout (no auth required)
Route::get('user/checkout/preview', [\App\Http\Controllers\Api\User\CheckoutController::class, 'preview'])
    ->name('api.user.checkout.preview.public');

// Checkout Create - allows guest checkout with billing_email
Route::post('user/checkout', [\App\Http\Controllers\Api\User\CheckoutController::class, 'create'])
    ->middleware('throttle:20,1')
    ->name('api.user.checkout.create.guest');

/*
|--------------------------------------------------------------------------
| Admin Routes (API Secret Key Required)
|--------------------------------------------------------------------------
| Admin operations - manage products, prices, settings, view all data
*/

Route::prefix('admin')->middleware(['api.key', 'throttle:120,1'])->group(function () {

    // User management
    Route::prefix('users')->group(function () {
        Route::post('/sync', [UserTokenController::class, 'generateToken'])
            ->name('api.integrate.users.sync');

        Route::controller(\App\Http\Controllers\Api\Admin\UserController::class)->group(function () {
            Route::get('/', 'index')->name('api.admin.users.index');
            Route::post('/', 'store')->name('api.admin.users.store');
            Route::get('/{user}', 'show')->name('api.admin.users.show');
            Route::put('/{user}', 'update')->name('api.admin.users.update');
            Route::delete('/{user}', 'destroy')->name('api.admin.users.destroy');
        });
    });

    // Product management
    Route::prefix('products')->controller(\App\Http\Controllers\Api\Admin\ProductController::class)->group(function () {
        Route::get('/', 'index')->name('api.admin.products.index');
        Route::post('/', 'store')->name('api.admin.products.store');
        Route::get('/{product}', 'show')->name('api.admin.products.show');
        Route::put('/{product}', 'update')->name('api.admin.products.update');
        Route::delete('/{product}', 'destroy')->name('api.admin.products.destroy');

        // Price management
        Route::get('/{product}/prices', 'priceIndex')->name('api.admin.products.prices.index');
        Route::post('/{product}/prices', 'priceStore')->name('api.admin.products.prices.store');
        Route::put('/{product}/prices/{price}', 'priceUpdate')->name('api.admin.products.prices.update');
        Route::delete('/{product}/prices/{price}', 'priceDestroy')->name('api.admin.products.prices.destroy');
    });

    // Order management (admin view - all orders)
    Route::prefix('orders')->controller(\App\Http\Controllers\Api\Admin\OrderController::class)->group(function () {
        Route::get('/', 'index')->name('api.admin.orders.index');
        Route::get('/{order}', 'show')->name('api.admin.orders.show');
    });

    // Settings management
    Route::prefix('settings')->controller(\App\Http\Controllers\Api\SettingsController::class)->group(function () {
        Route::get('/', 'index')->name('api.admin.settings.index');
        Route::put('/', 'update')->name('api.admin.settings.update');
    });

    // Transactions management
    Route::prefix('transactions')->controller(\App\Http\Controllers\Api\Admin\TransactionController::class)->group(function () {
        Route::get('/', 'index')->name('api.admin.transactions.index');
        Route::get('/{transaction}', 'show')->name('api.admin.transactions.show');
    });
});

/*
|--------------------------------------------------------------------------
| Webhook Routes
|--------------------------------------------------------------------------
| Payment provider webhooks (signature validated, no API key required)
*/

Route::prefix('webhooks')->controller(\App\Http\Controllers\Api\WebhookController::class)->group(function () {
    Route::post('/stripe', 'stripe')->name('api.webhooks.stripe');
    Route::post('/paypal', 'paypal')->name('api.webhooks.paypal');
});
