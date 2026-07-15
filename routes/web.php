<?php

use App\Http\Controllers\AccountModalsDemoController;
use App\Http\Controllers\CheckoutPageController;
use App\Http\Controllers\CheckoutPageDemoController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\PortalDemoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Installation routes
Route::middleware('web')->group(function () {
    Route::get('/install', [InstallController::class, 'welcome'])->name('install.welcome');
    Route::get('/install/requirements', [InstallController::class, 'requirements'])->name('install.requirements');
    Route::get('/install/database', [InstallController::class, 'database'])->name('install.database');
    Route::post('/install/database/test', [InstallController::class, 'testDatabase'])->name('install.database.test');
    Route::post('/install/database', [InstallController::class, 'databaseStore'])->name('install.database.store');
    Route::get('/install/admin', [InstallController::class, 'admin'])->name('install.admin');
    Route::post('/install/admin', [InstallController::class, 'adminStore'])->name('install.admin.store');
    Route::get('/install/complete', [InstallController::class, 'complete'])->name('install.complete');
});

// Redirect to installer if not installed, otherwise to the admin login
Route::get('/', function () {
    if (! File::exists(storage_path('installed'))) {
        return redirect()->route('install.welcome');
    }

    return redirect()->route('filament.admin.auth.login');
})->name('home');

Route::get('/login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');

// Portal entry point - validates signed URL and returns Inertia SPA with JWT token
Route::get('portal', [PortalController::class, 'index'])
    ->name('portal');

// Portal demo - generates a test signed URL for demo user
Route::get('portal-demo', [PortalDemoController::class, 'index'])
    ->name('portal.demo');

// Add: Checkout SPA entry point (signed, stateless)
Route::get('checkout', [CheckoutPageController::class, 'index'])
    ->name('checkout');

// Add: Demo link to generate signed checkout URL
Route::get('checkout-demo', [CheckoutPageDemoController::class, 'index'])
    ->name('checkout.demo');

// Add: Payment success/cancel pages (stateless)
Route::get('payment/success', function (Request $request) {
    $orderId = $request->query('order');
    $token = $request->query('token'); // PayPal token

    // If PayPal token is present, capture the payment
    if ($token && $orderId) {
        $order = \App\Models\Order::find($orderId);
        if ($order && $order->gateway === 'paypal' && $order->status === 'pending') {
            try {
                $paypalGateway = \App\Services\Payment\PaymentGatewayFactory::create('paypal');

                // Capture the PayPal order
                $captureResult = $paypalGateway->captureOrder($token);

                if ($captureResult['success']) {
                    \Illuminate\Support\Facades\Log::info('PayPal order captured on return', [
                        'order_id' => $orderId,
                        'token' => $token,
                    ]);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to capture PayPal order on return', [
                    'order_id' => $orderId,
                    'token' => $token,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    return Inertia::render('Checkout/Success', [
        'orderId' => $orderId,
        'apiBaseUrl' => config('app.url'),
        'clientUrl' => settings()->get('app.client_url') ?: null,
    ]);
})->name('payment.success');

Route::get('payment/cancel', function (Request $request) {
    $orderId = $request->query('order');
    $token = $request->query('token');
    $cancelled = false;
    $error = null;

    // Validate signed URL - prevents tampering
    if (! $request->hasValidSignature()) {
        \Illuminate\Support\Facades\Log::warning('Invalid signature on cancel URL', [
            'order_id' => $orderId,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $error = 'Invalid or expired cancellation link';
    } elseif ($orderId && $token) {
        // Find order
        $order = \App\Models\Order::find($orderId);

        if (! $order) {
            $error = 'Order not found';
        } elseif (! $order->validateCancellationToken($token)) {
            // Token validation failed (expired, invalid, or already used)
            \Illuminate\Support\Facades\Log::warning('Invalid cancellation token', [
                'order_id' => $orderId,
                'order_number' => $order->order_number ?? null,
                'ip' => $request->ip(),
            ]);

            $error = 'Invalid or expired cancellation token';
        } elseif ($order->status === 'cancelled') {
            // Already cancelled - idempotent
            $cancelled = true;
        } elseif ($order->status === 'pending') {
            // Cancel the order
            $order->update([
                'status' => 'cancelled',
                'meta' => array_merge($order->meta ?? [], [
                    'cancelled_at' => now()->toIso8601String(),
                    'cancelled_by' => 'user_web',
                    'cancelled_from' => 'payment_gateway_redirect',
                    'cancelled_from_ip' => $request->ip(),
                ]),
            ]);

            // Invalidate token (one-time use)
            $order->invalidateCancellationToken();

            $cancelled = true;

            \Illuminate\Support\Facades\Log::info("Order {$order->order_number} cancelled via secure payment gateway redirect", [
                'order_id' => $order->id,
                'ip' => $request->ip(),
            ]);
        } else {
            $error = "Cannot cancel order with status: {$order->status}";
        }
    }

    return Inertia::render('Checkout/Cancel', [
        'orderId' => $orderId,
        'cancelled' => $cancelled,
        'error' => $error,
        'apiBaseUrl' => config('app.url'),
        'clientUrl' => settings()->get('app.client_url') ?: null,
    ]);
})->name('payment.cancel');

// Checkout Modal Web Component Demo
Route::get('checkout-modal-demo', function () {
    // Get a sample product and price for demo
    $product = \App\Models\Product::with('prices')->active()->latest()->first();
    if (! $product || $product->prices->isEmpty()) {
        return response()->view('errors.no-products', [
            'message' => 'No active products found.',
            'instructions' => 'Please create a product with prices in the Filament admin panel first.',
            'adminUrl' => url('/admin/products'),
        ], 404);
    }

    $price = $product->prices->first();

    return view('checkout-modal-demo', [
        'productId' => $product->id,
        'priceId' => $price->id,
    ]);
})->name('checkout-modal.demo');

// Account Modals Web Component Demo (Subscriptions, Orders, Transactions)
Route::get('account-modals-demo', [AccountModalsDemoController::class, 'index'])
    ->name('account-modals.demo');
