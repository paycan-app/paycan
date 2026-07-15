# PayCan API Structure

PayCan is an integration system for payment needs. External apps sync their users with PayCan and use our APIs to manage payments on behalf of users.

## User Types & Authentication

### Admin Users vs. Regular Users

PayCan separates **admin users** (staff who manage the system) from **regular users** (customers synced from external apps):

- **Admin Users**: Stored in `admin_users` table, authenticate via Filament admin panel at `/admin` using the `admin` guard
- **Regular Users**: Stored in `users` table, authenticate via Sanctum tokens for API access
- **Complete Separation**: Admin and regular users are completely isolated - they use different tables, different authentication guards, and have different permissions

### Authentication Types

#### 1. Admin Panel Authentication (Admin Users Only)
- **Login URL**: `/admin`
- **Method**: Session-based authentication using `admin` guard
- **User Model**: `AdminUser`
- **Access**: Filament admin panel for managing products, prices, orders, settings, etc.
- **Creation**:
  - Web installer: `/install`
  - CLI command: `php artisan make:admin-user`

#### 2. JWT Token (User-Scoped Operations)
- **Header**: `Authorization: Bearer <jwt_token>`
- **Get Token**: `POST /api/admin/users/sync` (requires API secret key)
- **User Model**: `User`
- **Allows**: Create orders, manage subscriptions, view licenses, downloads
- **Does NOT Allow**: Admin settings, product/price management, Filament panel access

#### 3. API Secret Key (Admin Operations)
- **Header**: `X-API-Key: <api_secret_key>` (required)
- **Query Parameter**: `?api_key=<api_secret_key>` (only in local development environment, for debugging)
- **Get Key**: Admin Panel > Settings > API Secret Key
- **Allows**: Manage products, prices, settings, view all data via API
- **Note**: This is for programmatic admin access, separate from admin user authentication
- **Important**: Admin API does NOT support `Authorization: Bearer` token authentication. Only `X-API-Key` header is supported (this is intentional design).

---

## API Routes

### Integration Partner Routes

#### Sync User & Generate Token (API Secret Key Required)
```
POST /api/admin/users/sync
Headers: X-API-Key: <api_secret_key>
Body: {
  "user_id": "external-user-123",
  "user": {
    "name": "John Doe",
    "email": "john@example.com"
  }
}
Response: {
  "token": "jwt_token_here",
  "user": {...}
}
```

#### Get Products (Public)
```
GET /api/user/products
GET /api/user/products/{product}
```

---

### User-Scoped Routes (JWT Token Required)

All routes require: `Authorization: Bearer <jwt_token>`

#### User Info
```
GET /api/user/me
```

#### Orders
```
GET  /api/user/orders                    # List user's orders
GET  /api/user/orders/{order}            # Get specific order
GET  /api/user/orders/{order}/downloads  # Get download links for order
GET  /api/user/orders/{order}/licenses   # Get license keys for order
```

#### Subscriptions
```
GET  /api/user/subscriptions                    # List user's subscriptions
POST /api/user/subscriptions                    # Create subscription
GET  /api/user/subscriptions/{subscription}     # Get subscription
POST /api/user/subscriptions/{id}/cancel        # Cancel subscription
POST /api/user/subscriptions/{id}/resume        # Resume subscription
POST /api/user/subscriptions/{id}/change        # Change plan (PayPal may return an approval_url the customer must visit)
```

#### Checkout
```
GET  /api/user/checkout/preview        # Preview totals and payment methods (public)
POST /api/user/checkout                # Create checkout session (works for guests via billing_email)
POST /api/user/checkout/portal         # Get customer portal URL
POST /api/user/checkout/{order}/cancel # Cancel a pending order
```

> **Tax**: PayCan does not calculate tax. Tax is calculated and collected by the
> payment gateway at checkout — enable **Automatic Tax (Stripe Tax)** in the Stripe
> settings, or configure tax in your PayPal account. Recorded order totals always
> match the pre-tax amount sent to the gateway.

---

### Admin Routes (API Secret Key Required)

All routes require: `X-API-Key: <api_secret_key>` header (or `?api_key=<api_secret_key>` query parameter in local development only)

**Note**: Admin API does NOT support `Authorization: Bearer` token authentication. Only `X-API-Key` header is supported (this is intentional design).

#### Product Management
```
GET    /api/admin/products              # List all products (including inactive)
POST   /api/admin/products              # Create product
GET    /api/admin/products/{product}    # Get product details
PUT    /api/admin/products/{product}    # Update product
DELETE /api/admin/products/{product}    # Delete product
```

#### Order Management
```
GET /api/admin/orders         # List all orders
GET /api/admin/orders/{order} # Get order details
```

#### Settings Management
```
GET /api/admin/settings  # Get all settings
PUT /api/admin/settings  # Update settings
```

---

### Webhooks

No authentication required (signature validated by payment provider)

```
POST /api/webhooks/stripe
POST /api/webhooks/paypal
```

---

## Integration Flow

### Step 1: Partner App Syncs User
```bash
# Partner app calls with their API secret key
curl -X POST https://paycan.app/api/admin/users/sync \
  -H "X-API-Key: your_api_secret_key" \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": "user-123",
    "user": {
      "name": "John Doe",
      "email": "john@example.com"
    }
  }'

# Response includes JWT token
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": {
    "id": "user-123",
    "name": "John Doe",
    "email": "john@example.com"
  }
}
```

### Step 2: Partner App Makes Requests on Behalf of User
```bash
# Using the JWT token from step 1, create a checkout session
curl -X POST https://paycan.app/api/user/checkout \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc..." \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": "product-123",
    "product_price_id": "price-123",
    "gateway": "stripe"
  }'
```

### Step 3: Partner App Manages Products (Admin)
```bash
# Using API secret key
curl -X POST https://paycan.app/api/admin/products \
  -H "X-API-Key: your_api_secret_key" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Premium Plan",
    "slug": "premium-plan",
    "type": "subscription",
    "is_active": true
  }'
```

---

## Security Model

- **User-scoped operations**: JWT tokens only allow operations on behalf of the authenticated user
- **Admin operations**: API secret key allows full access to manage products, settings, and view all data
- **Separation of concerns**: Integration partners cannot access admin panel or settings via JWT tokens
- **Rate limiting**: 60 requests/minute for integration routes, 120 requests/minute for user/admin routes

---

## Payment Gateway Architecture

PayCan follows a clean architecture pattern that separates payment gateway integration (infrastructure) from business logic (domain services). This makes it easy to add new payment gateways without exposing system internals.

### Architecture Layers

```
┌─────────────────────────────────────────────────────────────────┐
│                    Controllers (HTTP Layer)                      │
│                   WebhookController.php                          │
│  - Validates webhook signatures                                  │
│  - Delegates to payment gateway                                  │
└───────────────────────────┬─────────────────────────────────────┘
                            │
┌───────────────────────────▼─────────────────────────────────────┐
│              Infrastructure Layer (Gateways)                     │
│         StripeGateway.php | PayPalGateway.php                   │
│  - API communication only                                        │
│  - Webhook event parsing                                         │
│  - Delegates business logic to WebhookProcessingService          │
└───────────────────────────┬─────────────────────────────────────┘
                            │
┌───────────────────────────▼─────────────────────────────────────┐
│            Orchestration Layer (Service)                         │
│            WebhookProcessingService.php                          │
│  - Routes webhook events to handlers                             │
│  - Orchestrates business logic                                   │
│  - Delegates to domain services                                  │
└───────────┬────────────────────────────────────┬────────────────┘
            │                                    │
┌───────────▼──────────────┐      ┌─────────────▼───────────────┐
│   Domain Layer (Logic)   │      │   Domain Layer (Logic)      │
│    OrderService.php      │      │  SubscriptionService.php    │
│  - Order status updates  │      │  - Subscription activation  │
│  - Transaction creation  │      │  - Status updates           │
│  - Business rules        │      │  - Transaction creation     │
└───────────┬──────────────┘      └─────────────┬───────────────┘
            │                                    │
            └────────────────┬───────────────────┘
                             │
┌────────────────────────────▼────────────────────────────────────┐
│              Side Effects Layer (Observers)                      │
│                   OrderObserver.php                              │
│  - Fulfillment processing                                        │
│  - Notification emails                                           │
│  - Admin alerts                                                  │
└─────────────────────────────────────────────────────────────────┘
```

### Key Services

#### 1. OrderService
Located: `app/Services/Order/OrderService.php`

Handles all order-related business logic:
- `markOrderAsPaid()` - Transitions order from pending to paid
- `createTransactionForOrder()` - Creates payment transaction records
- Idempotent operations (prevents duplicate processing)

#### 2. SubscriptionService
Located: `app/Services/Subscription/SubscriptionService.php`

Manages subscription lifecycle:
- `activateSubscription()` - Activates subscription after payment
- `updateSubscriptionStatus()` - Updates status (active, canceled, etc.)
- `createSubscriptionTransaction()` - Creates subscription payment records
- `cancelSubscription()` - Handles subscription cancellation

#### 3. WebhookProcessingService
Located: `app/Services/Payment/WebhookProcessingService.php`

Orchestrates webhook processing:
- `processStripeWebhook()` - Routes Stripe events
- `processPayPalWebhook()` - Routes PayPal events
- Delegates to OrderService and SubscriptionService
- Single source of truth for payment processing logic

---

## Adding a New Payment Gateway

Adding a new payment gateway is simple - you only need to handle API communication and webhook parsing. All business logic is handled automatically.

### Step 1: Create Gateway Class

Create a new file in `app/Services/Payment/`:

```php
<?php

namespace App\Services\Payment;

use App\Services\Payment\Contracts\PaymentGatewayInterface;

class YourGateway implements PaymentGatewayInterface
{
    public function createPayment(array $data): array
    {
        // 1. Make API call to your payment provider
        $response = $this->callYourApi('/create-payment', $data);

        // 2. Return checkout URL and session ID
        return [
            'success' => true,
            'checkout_url' => $response['checkout_url'],
            'session_id' => $response['session_id'],
        ];
    }

    public function handleWebhook(array $payload): array
    {
        // Parse webhook and delegate to WebhookProcessingService
        $webhookService = app(WebhookProcessingService::class);
        return $webhookService->processYourGatewayWebhook($payload);
    }

    public function refund(string $transactionId, float $amount): array
    {
        // Handle refund API call
        return ['success' => true];
    }

    public function cancelSubscription(string $subscriptionId, bool $immediately = false): array
    {
        // Handle subscription cancellation API call
        // $immediately = false should cancel at the end of the billing period
        return ['success' => true];
    }
}
```

### Step 2: Add Webhook Processing

Add a method to `WebhookProcessingService.php`:

```php
public function processYourGatewayWebhook(array $event): array
{
    return match ($event['event_type'] ?? null) {
        'payment.completed' => $this->handleYourGatewayPaymentCompleted($event),
        'subscription.activated' => $this->handleYourGatewaySubscriptionActivated($event),
        // Add more event types as needed
        default => ['success' => true, 'message' => 'Unhandled event type'],
    };
}

protected function handleYourGatewayPaymentCompleted(array $payload): array
{
    // 1. Find the order
    $orderId = $payload['order_id']; // Your gateway's order ID field
    $order = Order::where('gateway_order_id', $orderId)->first();

    if (!$order) {
        return ['success' => false, 'error' => 'Order not found'];
    }

    // 2. Mark order as paid (OrderService handles all business logic)
    $this->orderService->markOrderAsPaid($order, [
        'gateway_payment_id' => $payload['payment_id'],
        'gateway_status' => $payload['status'],
    ]);

    // 3. Create transaction (OrderService prevents duplicates)
    $this->orderService->createTransactionForOrder($order, [
        'gateway_transaction_id' => $payload['payment_id'],
        'amount' => $payload['amount'],
        'currency' => $payload['currency'],
        'type' => 'payment',
        'status' => 'completed',
    ]);

    // 4. Handle subscription if recurring
    if ($order->productPrice && $order->productPrice->billing_period !== 'once') {
        $subscription = Subscription::where('order_id', $order->id)->first();
        if ($subscription) {
            $this->subscriptionService->activateSubscription($subscription);
        }
    }

    return ['success' => true, 'message' => 'Payment processed'];
}
```

### Step 3: Register Gateway

Add to `PaymentGatewayFactory.php`:

```php
public static function create(string $gateway): PaymentGatewayInterface
{
    return match ($gateway) {
        'stripe' => new StripeGateway,
        'paypal' => new PayPalGateway,
        'yourgateway' => new YourGateway, // Add this line
        default => throw new \InvalidArgumentException("Unsupported gateway: {$gateway}"),
    };
}
```

### Step 4: Add Webhook Route

Add to `routes/api.php`:

```php
Route::prefix('webhooks')->controller(\App\Http\Controllers\Api\WebhookController::class)->group(function () {
    Route::post('/stripe', 'stripe')->name('api.webhooks.stripe');
    Route::post('/paypal', 'paypal')->name('api.webhooks.paypal');
    Route::post('/yourgateway', 'yourgateway')->name('api.webhooks.yourgateway'); // Add this
});
```

### Step 5: Add Webhook Controller Method

Add to `WebhookController.php`:

```php
public function yourgateway(Request $request): JsonResponse
{
    try {
        // 1. Verify webhook signature (your gateway's method)
        $isValid = $this->verifyYourGatewaySignature($request);
        if (!$isValid) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // 2. Parse payload
        $payload = json_decode($request->getContent(), true);

        // 3. Process webhook (business logic handled automatically)
        $gateway = PaymentGatewayFactory::create('yourgateway');
        $result = $gateway->handleWebhook($payload);

        return response()->json([
            'received' => true,
            'message' => 'Webhook processed successfully',
        ]);
    } catch (\Exception $e) {
        Log::error('Error processing webhook', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'Error processing webhook'], 500);
    }
}
```

### What You DON'T Need to Worry About

The following are handled automatically by the domain services:

- ✅ Order status updates
- ✅ Subscription activation and lifecycle
- ✅ Transaction creation and duplicate prevention
- ✅ Order fulfillment (digital products, licenses, etc.)
- ✅ Email notifications to users and admins
- ✅ Database consistency and transactions
- ✅ Idempotent operations (safe to process webhooks multiple times)

### Testing Your Gateway

Create a Pest test in `tests/Feature/`:

```php
it('processes payment webhook from your gateway', function () {
    $order = Order::factory()->create([
        'status' => 'pending',
        'gateway' => 'yourgateway',
        'gateway_order_id' => 'YOUR-ORDER-123',
    ]);

    $webhookPayload = [
        'event_type' => 'payment.completed',
        'order_id' => 'YOUR-ORDER-123',
        'payment_id' => 'PAY-123',
        'amount' => 100.00,
        'currency' => 'USD',
    ];

    $webhookService = app(WebhookProcessingService::class);
    $result = $webhookService->processYourGatewayWebhook($webhookPayload);

    expect($result['success'])->toBeTrue();
    expect($order->refresh()->status)->toBe('paid');
});
```

### Benefits of This Architecture

1. **Clean Separation**: Payment gateways only handle API communication
2. **Easy Integration**: Add new gateways in ~100 lines of code
3. **Consistent Behavior**: All gateways use same business logic
4. **Testable**: Mock gateway API, test business logic independently
5. **Maintainable**: Business rules in one place, easy to update
6. **Reliable**: Idempotent operations prevent duplicate processing

---

## Webhook Event Flow

```
1. Payment Provider → POST /api/webhooks/yourgateway
2. WebhookController → Verify signature
3. WebhookController → PaymentGatewayFactory::create('yourgateway')
4. YourGateway → handleWebhook()
5. YourGateway → WebhookProcessingService::processYourGatewayWebhook()
6. WebhookProcessingService → Route to specific handler (e.g., payment.completed)
7. WebhookProcessingService → OrderService::markOrderAsPaid()
8. OrderService → Update order status in database
9. WebhookProcessingService → OrderService::createTransactionForOrder()
10. OrderService → Create transaction (with duplicate prevention)
11. WebhookProcessingService → SubscriptionService::activateSubscription() (if recurring)
12. SubscriptionService → Activate subscription
13. OrderObserver → Triggered by order status change
14. OrderObserver → FulfillmentService::processPurchaseFulfillment()
15. OrderObserver → Send email notifications
16. Return success response to payment provider
```

### Important Notes

- **Idempotency**: All services check for existing records before creating
- **Database Transactions**: Critical operations wrapped in DB::transaction()
- **Error Handling**: Services log errors and return appropriate responses
- **Eager Loading**: Load relationships to prevent N+1 queries
- **Status Validation**: Services verify valid status transitions

## Canonical Subscription Statuses and Gateway Mapping

Paycan exposes a consistent set of internal subscription statuses:

- `active`
- `trialing`
- `past_due`
- `canceled`
- `paused`
- `expired`
- `incomplete`

Gateway-specific state is stored in `gateway_status` on the subscription record and is not part of the canonical status vocabulary.

Examples:

- Stripe
  - Cancel at period end → `status=canceled`, `ends_at=current_period_end` (timestamp from Stripe)
  - Immediate cancel → `status=canceled`, `ends_at=now()`
  - Resume → `status=active`, clears `canceled_at` and `ends_at`

- PayPal
  - Suspend → `status=canceled`, `gateway_status=SUSPENDED`, `current_period_end=next_billing_time` (ISO8601)
  - Cancel → `status=canceled`, `gateway_status=CANCELLED`, non-resumable
  - Resume from suspended → `status=active`, `gateway_status=ACTIVE`

### Resume Behavior

API responses include `can_resume`:

- `can_resume = (status === 'canceled') AND (ends_at is in the future OR gateway-specific rule indicates resumable)`

Gateway-specific rule examples:

- PayPal: `gateway_status === 'SUSPENDED'` is resumable.
- Stripe: resumable only when canceled at period end (reflected by `ends_at` in the future).

This provides a single UI rule for “Resume Subscription” across all gateways without introducing new internal statuses.
