# 🚀 Quick Start - Portal Demo

## ⚡ 30-Second Setup

### 1. Seed Demo Data
```bash
php artisan db:seed --class=PortalDemoSeeder
```

### 2. Start Server
```bash
php artisan serve
```

### 3. Open Demo
```
http://localhost:8000/portal-demo
```

That's it! 🎉

---

## 📦 What You'll See

The demo page includes:

✅ **4 Sample Products**
- Digital E-book ($49.99)
- Consulting Service ($150/hr)
- SaaS Subscription ($29.99/mo)
- Physical T-Shirt ($24.99)

✅ **Live Portal Embedded in Iframe**
- Browse products
- Test checkout flow
- View orders
- Manage subscriptions

✅ **Ready-to-Copy Code**
- Portal URL (signed, expires in 24h)
- HTML embed code
- Integration examples

---

## 🎯 Test Features

### In the Portal (iframe):

1. **Products Page** (`/portal`)
   - View 4 different product types
   - See prices and descriptions
   - Click "Buy" buttons

2. **Checkout Page** (`/portal/checkout/{price}`)
   - Select payment method (Stripe/PayPal)
   - Enter shipping address (physical products only)
   - Preview order

3. **Orders Page** (`/portal/orders`)
   - View purchase history
   - Download digital products
   - Copy license keys

4. **Subscriptions Page** (`/portal/subscriptions`)
   - View active subscriptions
   - Cancel/Resume subscriptions
   - Change plans
   - Manage payment methods

---

## 🔐 How It Works

### Secure Authentication
```php
// Backend generates signed URL
$portalUrl = PortalService::generatePortalUrl($userId, 24);
```

The URL:
- ✅ Is cryptographically signed
- ✅ Expires after 24 hours
- ✅ Is tied to specific user
- ✅ Cannot be tampered with

### Iframe Embedding
```html
<iframe src="{{ $portalUrl }}" width="100%" height="800"></iframe>
```

The portal:
- ✅ Works in any iframe
- ✅ Is fully responsive
- ✅ Has secure CSP headers
- ✅ Supports dark mode

---

## 📖 Routes

### Demo Page
```
GET /portal-demo
```
Shows the demo with embedded portal

### Portal Routes (require signed URL)
```
GET  /portal                              # Products listing
GET  /portal/checkout/{price}             # Checkout page
POST /portal/checkout/{price}             # Process checkout
GET  /portal/orders                       # Orders list
GET  /portal/orders/{order}/downloads     # Download links
GET  /portal/orders/{order}/licenses      # License keys
GET  /portal/subscriptions                # Subscriptions list
POST /portal/subscriptions/{id}/cancel    # Cancel subscription
POST /portal/subscriptions/{id}/resume    # Resume subscription
POST /portal/subscriptions/{id}/change    # Change plan
GET  /portal/subscriptions/{id}/portal    # Gateway portal URL
```

---

## 🔧 Customization

### Change URL Expiration
```php
// In PortalDemoBladeController.php
$portalUrl = PortalService::generatePortalUrl($user->id, 48); // 48 hours
```

### Add More Products
```php
// In database/seeders/PortalDemoSeeder.php
Product::create([...]);
ProductPrice::create([...]);
```

Then re-run: `php artisan db:seed --class=PortalDemoSeeder`

### Configure Security
```php
// In config/portal.php
'allowed_iframe_domains' => [
    'https://yourapp.com',
    'https://app.yourapp.com'
]
```

---

## 💡 Integration Example

### Your App Backend
```php
use App\Services\PortalService;

Route::get('/my-portal', function () {
    $user = auth()->user();
    $portalUrl = PortalService::generatePortalUrl($user->id);

    return view('portal', ['portalUrl' => $portalUrl]);
});
```

### Your App Frontend
```html
<!-- portal.blade.php -->
<div class="container">
    <h1>Your Payment Portal</h1>
    <iframe
        src="{{ $portalUrl }}"
        width="100%"
        height="800"
        frameborder="0"
    ></iframe>
</div>
```

---

## 🎯 SDK Integration

### JavaScript/TypeScript
```javascript
import PayCan from '@paycan/sdk'

// Initialize
const paycan = new PayCan({
  apiUrl: 'https://pay.yourapp.com'
})

// Authenticate
const response = await fetch('/api/paycan/token')
const { token } = await response.json()
paycan.setUserToken(token)

// Use the API
const products = await paycan.products.list({
  include: 'activePrices'
})

const orders = await paycan.orders.list()

const subscriptions = await paycan.subscriptions.list()
```

---

## 📚 Documentation

- **[DEMO.md](DEMO.md)** - Detailed demo guide
- **[PORTAL.md](PORTAL.md)** - Complete implementation guide
- **[README.md](README.md)** - Main project documentation

---

## 🐛 Troubleshooting

### "Demo user not found"
Run the seeder: `php artisan db:seed --class=PortalDemoSeeder`

### "No products showing"
The portal is empty - run the seeder to add sample products

### "Invalid signature"
The URL expired (24 hours). Refresh the demo page to generate a new URL.

### "Portal won't load in iframe"
Check `config/portal.php` - ensure `allowed_iframe_domains` is set to `'*'` for testing

---

## ✨ Production Checklist

Before going live:

- [ ] Build frontend: `npm run build`
- [ ] Configure allowed domains in `config/portal.php`
- [ ] Set environment variables (`.env`)
- [ ] Enable HTTPS (required for payments)
- [ ] Test with real payment gateways
- [ ] Review security settings
- [ ] Set appropriate URL expiration times
- [ ] Configure rate limiting

---

## 🎉 You're Ready!

Visit **http://localhost:8000/portal-demo** now and explore the portal!

**Need help?** Check the full documentation in [PORTAL.md](PORTAL.md)
