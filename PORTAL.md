# Payment Portal - Complete Guide

## Overview

The Payment Portal is a **fully client-side Single Page Application (SPA)** that allows your customers to browse products, make purchases, view orders, and manage subscriptions through an embeddable iframe.

### Key Features

- ✅ **Pure SPA Architecture** - All data fetching via SDK API
- ✅ **Fancy UI/UX** - Modern, responsive design with Tailwind v4 and shadcn-vue  
- ✅ **Secure Authentication** - Signed URLs with session-based authentication
- ✅ **Payment Gateway Integration** - Redirects parent window to Stripe/PayPal
- ✅ **Iframe-Embeddable** - Perfect for embedding in any website
- ✅ **Tab-Based Navigation** - Products, Orders, and Subscriptions
- ✅ **Fully Responsive** - Works on all devices

## Quick Start

### 1. Generate a Portal URL

```php
use App\Services\PortalService;

$userId = 'user-123';
$portalUrl = PortalService::generatePortalUrl($userId, 24); // Valid for 24 hours
```

### 2. Embed in Your Website

```html
<iframe
    src="<?= $portalUrl ?>"
    width="100%"
    height="800"
    frameborder="0"
    style="border: 1px solid #e5e7eb; border-radius: 8px;"
></iframe>
```

## Architecture

The portal is built as a **pure SPA** - all data is fetched client-side via the SDK API.

**Route:** `/portal`  
**Component:** `resources/js/pages/Portal/App.vue`

### Three Main Views

1. **ProductsView** - Browse & purchase
2. **OrdersView** - Order history & downloads  
3. **SubscriptionsView** - Manage subscriptions

### Payment Gateway Redirects

When user clicks "Buy", the portal:
1. Creates order via `/api/user/checkout`
2. Receives `gateway_data.checkout_url`
3. **Redirects parent window** (breaks out of iframe)
4. User completes payment on Stripe/PayPal
5. Returns to success/cancel URL

```javascript
// Parent window redirect logic
if (window.parent !== window) {
    window.parent.location.href = checkoutUrl;
} else {
    window.location.href = checkoutUrl;
}
```

## Demo

Visit `/portal-demo` for a live example with sample products.

## Security

- Signed URLs with expiration
- Session-based authentication after initial access
- Configurable iframe domain allowlist
- Rate limiting (120 req/min default)

## Configuration

```php
// config/portal.php
'allowed_iframe_domains' => env('PORTAL_ALLOWED_DOMAINS', '*'),
'link_expiration_hours' => env('PORTAL_LINK_EXPIRATION_HOURS', 24),
```

For production, set specific domains:
```env
PORTAL_ALLOWED_DOMAINS="https://example.com https://app.example.com"
```
