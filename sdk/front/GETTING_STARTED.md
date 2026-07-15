# Getting Started with the PayCan SDK

This guide will help you integrate PayCan into your application in just a few minutes.

## Prerequisites

- A running PayCan instance (see the [Installation Guide](../../INSTALLATION.md))
- Your PayCan API secret key (Admin Panel → Settings → API Secret Key)
- Node.js 18+ if bundling the SDK, or any modern browser for the hosted build

## Step-by-Step Integration

### Step 1: Install the SDK

**Option A – npm package:**

```bash
npm install @paycan/sdk
```

```javascript
import PayCan from '@paycan/sdk';
```

**Option B – hosted ES module (no build step):**

Your PayCan instance serves the built SDK directly:

```html
<script type="module">
  import { PayCan } from 'https://pay.yourapp.com/sdk/paycan-sdk.js';
</script>
```

A lightweight API-only build (checkout + checkout modal, no account resources) is also available as `paycan-sdk.api.js` — it exports `PayCanApi` instead of `PayCan`. Minified variants exist as `paycan-sdk.min.js` and `paycan-sdk.api.min.js`.

### Step 2: Create a Backend Token Endpoint

**IMPORTANT**: Never expose your PayCan API secret key in frontend code!

Create a secure endpoint in your backend that:
1. Verifies the user is authenticated in YOUR system (via middleware)
2. Gets the user ID from the session/JWT (NOT from the request body)
3. Calls PayCan's sync endpoint with your API secret key
4. Returns ONLY the token to your frontend

#### Example (Express.js):

```javascript
// server.js
app.post('/api/paycan/token', requireAuth, async (req, res) => {
  // SECURITY: Get user ID from session, NOT from request body!
  const userId = req.session.userId;

  // Get user from your database
  const user = await User.findById(userId);

  // Call PayCan
  const response = await fetch(`${process.env.PAYCAN_URL}/api/admin/users/sync`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-API-Key': process.env.PAYCAN_API_SECRET
    },
    body: JSON.stringify({
      user_id: user.id,
      user: {
        name: user.name,
        email: user.email
      }
    })
  });

  const data = await response.json();

  // Return ONLY the token
  res.json({ token: data.token });
});
```

See [examples/backend-auth-endpoint.js](examples/backend-auth-endpoint.js) for more examples (Next.js, Laravel, Django).

### Step 3: Initialize the SDK in Your Frontend

```javascript
import PayCan from '@paycan/sdk';

const paycan = new PayCan({
  apiUrl: 'https://pay.yourapp.com'
});
```

### Step 4: Set the Token on Page Load

```javascript
// On app user login (e.g., in App.js or index.js)
async function initPayCan() {
  const response = await fetch('/api/paycan/token');
  const { token } = await response.json();
  paycan.setUserToken(token);
}

initPayCan();
```

### Step 5: Use the API!

Now you can make API calls anywhere in your app:

```javascript
// Get user's orders with filtering and sorting
const orders = await paycan.orders.list({
  filter: { status: 'completed' },
  sort: '-created_at',
  include: 'productPrice.product',
  per_page: 20
});

// Active subscriptions (cached to avoid rate limits)
const activeSubscriptions = await paycan.subscriptions.listActive();
const hasPremium = activeSubscriptions.some(
  (sub) => sub.product?.name === 'Premium SaaS Plan'
);

// Browse active products
const products = await paycan.products.list({ include: 'activePrices' });

// Create a checkout session and redirect to the gateway
const session = await paycan.checkout.create({
  product_id: 'product-123',
  product_price_id: 'price-123',
  gateway: 'stripe'
});
window.location.href = session.checkout_url;
```

## Web Components (UI Modals)

Instead of building your own UI, you can use the SDK's built-in modal components. They are framework-agnostic, render inside a Shadow DOM (so your page styles are never affected), are fully responsive, and support `light`, `dark`, and `auto` themes.

| Component | Purpose | Auth required |
|---|---|---|
| `CheckoutModal` | Price + gateway selection, guest email, creates the checkout | No (guest checkout supported) |
| `ProductsModal` | Browse products/plans, opens checkout on selection | No |
| `SubscriptionsModal` | List, cancel, and resume subscriptions | Yes |
| `OrdersModal` | Order history with downloads and licenses | Yes |
| `TransactionsModal` | Payment history | Yes |

### Checkout and products modals

The `PayCan` client has helper methods for these:

```javascript
// Let the user pick a price for a product, then check out
paycan.openCheckoutModal(productId, {
  theme: 'dark', // 'light' (default) | 'dark' | 'auto'
  onSuccess: (checkoutUrl) => { window.location.href = checkoutUrl; },
  onCancel: () => console.log('User cancelled'),
  onError: (error) => console.error(error),
});

// Checkout for one specific price
paycan.openCheckoutModalPrice(priceId, {
  onSuccess: (checkoutUrl) => { window.location.href = checkoutUrl; }
});

// Browse products, optionally filtered by type
paycan.openProductsModal({
  type: 'subscription', // 'physical' | 'digital' | 'service' | 'subscription'
  theme: 'auto',
  onProductSelected: (product) => console.log('Selected:', product),
});
```

If no user token is set, the checkout modal automatically shows an email field and performs a guest checkout.

### Account modals

Instantiate these directly with your `PayCan` client (a user token must be set):

```javascript
import { PayCan, SubscriptionsModal, OrdersModal, TransactionsModal } from '@paycan/sdk';

new SubscriptionsModal(paycan, {
  theme: 'auto',
  onClose: () => console.log('closed'),
  onError: (error) => console.error(error),
}).open();

new OrdersModal(paycan, { theme: 'light' }).open();
new TransactionsModal(paycan, { theme: 'dark' }).open();
```

See the [Checkout Modal guide](../../CHECKOUT_MODAL_README.md) for full details, and try the live demos at `/checkout-modal-demo` and `/account-modals-demo` on your PayCan instance.

## Common Use Cases

### Use Case 1: Sell Digital Products

```javascript
// 1. User clicks "Buy Now" — simplest option: open the checkout modal
function buyProduct(productId) {
  paycan.openCheckoutModal(productId, {
    onSuccess: (checkoutUrl) => { window.location.href = checkoutUrl; }
  });
}

// Or create the checkout session yourself:
async function buyPrice(productId, priceId) {
  const session = await paycan.checkout.create({
    product_id: productId,
    product_price_id: priceId,
    gateway: 'stripe'
  });

  window.location.href = session.checkout_url;
}

// 2. After successful payment, show downloads
async function showDownloads(orderId) {
  const { downloads } = await paycan.orders.getDownloads(orderId);

  downloads.forEach(download => {
    console.log('Download:', download.product_title);
    console.log('Link:', download.download_url);
    console.log('License:', download.license_key);
  });
}
```

### Use Case 2: Subscription-Based Access

```javascript
// Check if the user has an active subscription to a product
async function checkPremiumAccess(productName) {
  const activeSubscriptions = await paycan.subscriptions.listActive();

  const hasAccess = activeSubscriptions.some(
    (sub) => sub.product?.name === productName
  );

  if (!hasAccess) {
    showUpgradeModal();
    return false;
  }

  return true;
}

// Use it before showing premium content
if (await checkPremiumAccess('Premium SaaS Plan')) {
  showPremiumContent();
}

// Has the user purchased anything?
const { data: orders } = await paycan.orders.list({ filter: { status: 'completed' } });
const isCustomer = orders.length > 0;
```

`listActive()` uses a short-lived cache (configurable via the `cacheTtl` option, default 60 seconds), so it is safe to call on every page view.

### Use Case 3: Manage Subscriptions

```javascript
// Simplest option: the built-in modal handles list/cancel/resume
new SubscriptionsModal(paycan, { theme: 'auto' }).open();

// Or build your own UI:
async function showSubscriptionManager() {
  const { data: subscriptions } = await paycan.subscriptions.list({
    filter: { status: 'active' },
    include: 'productPrice.product',
    sort: '-created_at'
  });

  subscriptions.forEach(sub => {
    if (sub.status === 'active') {
      showCancelButton(sub.id);
    }
  });
}

// Cancel / resume / change plan
await paycan.subscriptions.cancel(subscriptionId);
await paycan.subscriptions.resume(subscriptionId);
await paycan.subscriptions.change(subscriptionId, { product_price_id: 'price-456' });
```

### Advanced Filtering

```javascript
// Orders from a date range, sorted by total
const recentOrders = await paycan.orders.list({
  filter: {
    created_after: '2024-01-01',
    status: 'completed'
  },
  sort: '-total',
  per_page: 50
});

// Subscriptions by gateway
const stripeSubscriptions = await paycan.subscriptions.list({
  filter: { gateway: 'stripe' }
});

// Products by type
const digitalProducts = await paycan.products.list({
  filter: { type: 'digital' },
  include: 'activePrices',
  sort: 'title'
});
```

## Security Checklist

- [ ] API secret key is stored securely on the backend (never in frontend)
- [ ] Token endpoint is protected by authentication middleware
- [ ] User ID is retrieved from session/JWT, NOT from the request body
- [ ] Using HTTPS in production
- [ ] Rate limiting on your token endpoint
- [ ] Input validation on all user data

## Troubleshooting

### "Not authenticated" / "Session expired" Error

Make sure you've called `setUserToken()` before making API requests:

```javascript
// Get token from your backend
const response = await fetch('/api/paycan/token');
const { token } = await response.json();
paycan.setUserToken(token);

// Now you can make requests
const orders = await paycan.orders.list();
```

### "Failed to get token" Error

Check that:
1. Your `/api/paycan/token` endpoint is accessible
2. The user is logged in to YOUR system
3. The token endpoint returns the correct format: `{ token: "..." }`
4. Your PayCan API secret key is correct

### Token Expiry

The SDK automatically refreshes JWT tokens shortly before they expire (configurable via the `refreshThreshold` option, default 300 seconds). If you're still getting auth errors, enable debug mode:

```javascript
const paycan = new PayCan({
  apiUrl: 'https://pay.yourapp.com',
  debug: true // See what's happening
});
```

## Next Steps

- Read the [main project README](../../README.md)
- Check out the [example integrations](examples/)
- Learn about the [modal web components](../../CHECKOUT_MODAL_README.md)
- Browse the [full API reference](../../API_STRUCTURE.md)
