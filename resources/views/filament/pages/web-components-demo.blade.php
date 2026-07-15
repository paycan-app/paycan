<x-filament-panels::page>
    <style>
        .demo-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        @media (max-width: 1024px) {
            .demo-section {
                grid-template-columns: 1fr;
            }
        }
        .demo-code {
            position: relative;
        }
        .demo-code pre {
            margin: 0;
            padding: 1rem;
            background: rgb(31 41 55);
            border-radius: 0.5rem;
            overflow-x: auto;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.875rem;
            line-height: 1.5;
        }
        .demo-code code {
            color: rgb(243 244 246);
        }
        .dark .demo-code pre {
            background: rgb(17 24 39);
        }
        .copy-button {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
            background: rgb(55 65 81);
            color: white;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .copy-button:hover {
            background: rgb(75 85 99);
        }
        .copy-button.copied {
            background: rgb(34 197 94);
        }
        .auth-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .auth-badge.authenticated {
            background: rgb(220 252 231);
            color: rgb(22 101 52);
        }
        .dark .auth-badge.authenticated {
            background: rgb(6 78 59);
            color: rgb(167 243 208);
        }
        .auth-badge.guest {
            background: rgb(243 244 246);
            color: rgb(75 85 99);
        }
        .dark .auth-badge.guest {
            background: rgb(55 65 81);
            color: rgb(209 213 219);
        }
        .auth-indicator {
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 9999px;
            background-color: currentColor;
        }
        .needs-auth {
            opacity: 0.5;
        }
    </style>

    @if (!$productId)
    <x-filament::section>
        <x-slot name="heading">
            No Products Available
        </x-slot>
        <x-slot name="description">
            Please create at least one active product with prices to see all demo features.
        </x-slot>
    </x-filament::section>
    @endif

    <!-- Quick Setup -->
    <x-filament::section collapsible>
        <x-slot name="heading">
            Quick Setup Guide
        </x-slot>
        <x-slot name="description">
            Follow these steps to integrate PayCan SDK into your application.
        </x-slot>

        <div class="space-y-6">
            <div class="demo-section">
                <div>
                    <h3 class="text-base font-semibold mb-2">Step 1: Include SDK</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Import the hosted ES module straight from your PayCan instance — no build step needed.</p>
                </div>
                <div class="demo-code">
                    <button type="button" class="copy-button" data-copy-target="setup-1">Copy</button>
                    <pre id="setup-1"><code>&lt;script type="module"&gt;
  import { PayCan } from '{{ config('app.url') }}/sdk/paycan-sdk.js';

  const paycan = new PayCan({
    apiUrl: '{{ config('app.url') }}',
  });

  window.paycan = paycan;
&lt;/script&gt;</code></pre>
                </div>
            </div>

            <div class="demo-section">
                <div>
                    <h3 class="text-base font-semibold mb-2">Step 2: Get Token from Backend</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Never expose your API secret in frontend! Always get tokens from your backend, and take the user from <em>your</em> session — never from the request body.</p>
                </div>
                <div class="demo-code">
                    <button type="button" class="copy-button" data-copy-target="setup-2">Copy</button>
                    <pre id="setup-2"><code>// Backend endpoint (Express.js example)
app.post('/api/paycan/token', requireAuth, async (req, res) => {
  const response = await fetch('{{ config('app.url') }}/api/admin/users/sync', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-API-Key': process.env.PAYCAN_API_SECRET, // secret key: backend only!
    },
    body: JSON.stringify({
      user_id: req.session.userId, // from YOUR session — not the request body
      user: { name: req.user.name, email: req.user.email },
    }),
  });

  const { token } = await response.json();
  res.json({ token }); // return ONLY the token to the browser
});</code></pre>
                </div>
            </div>

            <div class="demo-section">
                <div>
                    <h3 class="text-base font-semibold mb-2">Step 3: Set Token</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Initialize the SDK with the user token on app load, or right after your login flow succeeds. Without a token, checkouts still work as guest.</p>
                </div>
                <div class="demo-code">
                    <button type="button" class="copy-button" data-copy-target="setup-3">Copy</button>
                    <pre id="setup-3"><code>// On app load
async function initPayCan() {
  const response = await fetch('/api/paycan/token');
  const { token } = await response.json();
  paycan.setUserToken(token);
}

initPayCan();</code></pre>
                </div>
            </div>
        </div>
    </x-filament::section>

    <!-- Authentication -->
    <x-filament::section collapsible>
        <x-slot name="heading">
            Authentication Management
        </x-slot>
        <x-slot name="description">
            Manage SDK authentication state for testing.
        </x-slot>

        <div class="demo-section">
            <div>
                <p class="text-sm font-medium mb-2">Current Status:</p>
                <span id="authStatusBadge" class="auth-badge guest">
                    <span class="auth-indicator"></span>
                    <span data-auth-label>Guest Mode</span>
                </span>
                <div id="userInfoDisplay" style="display: none;" class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Authenticated as: <strong>{{ $demoUser->email }}</strong>
                </div>

                <div class="mt-4">
                    <p class="text-sm font-medium mb-2">Actions:</p>
                    <div class="flex flex-wrap gap-2">
                        <x-filament::button data-demo-action="login">
                            Login as Demo User
                        </x-filament::button>
                        <x-filament::button data-demo-action="logout" color="danger">
                            Logout
                        </x-filament::button>
                        <x-filament::button data-demo-action="check-status" color="gray">
                            Check Status
                        </x-filament::button>
                    </div>
                </div>
            </div>

            <div>
                <p class="text-sm font-medium mb-2">Code:</p>
                <div class="demo-code">
                    <button type="button" class="copy-button" data-copy-target="auth-code">Copy</button>
                    <pre id="auth-code"><code>// Login
paycan.setUserToken(token);

// Logout
paycan.logout();

// Check status
paycan.isAuthenticated(); // boolean</code></pre>
                </div>
            </div>
        </div>
    </x-filament::section>

    @if ($productId)
    <!-- Checkout - Guest -->
    <x-filament::section collapsible collapsed>
        <x-slot name="heading">
            Checkout Modal - Guest
        </x-slot>
        <x-slot name="description">
            Test checkout flow for unauthenticated users.
        </x-slot>

        <div class="demo-section">
            <div>
                <p class="text-sm font-medium mb-2">Product Checkout:</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Opens modal showing all prices. Guests are asked for an email automatically.</p>
                <div class="flex flex-wrap gap-2 mb-4">
                    <x-filament::button data-demo-action="checkout-product" data-theme="light">
                        Light
                    </x-filament::button>
                    <x-filament::button data-demo-action="checkout-product" data-theme="dark" color="gray">
                        Dark
                    </x-filament::button>
                </div>

                <p class="text-sm font-medium mb-2 mt-6">Specific Price:</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Direct checkout for specific price.</p>
                <div class="flex flex-wrap gap-2">
                    <x-filament::button data-demo-action="checkout-price" data-theme="light">
                        Light
                    </x-filament::button>
                    <x-filament::button data-demo-action="checkout-price" data-theme="dark" color="gray">
                        Dark
                    </x-filament::button>
                </div>
            </div>

            <div>
                <p class="text-sm font-medium mb-2">Code:</p>
                <div class="demo-code">
                    <button type="button" class="copy-button" data-copy-target="checkout-guest">Copy</button>
                    <pre id="checkout-guest"><code>// Guest checkout works without authentication —
// the SDK shows an email field automatically.
paycan.openCheckoutModal('{{ $productId }}', {
  theme: 'light',
  onSuccess: (checkoutUrl) => {
    window.location.href = checkoutUrl;
  },
});

// Or open checkout for one specific price:
paycan.openCheckoutModalPrice('{{ $priceId }}', {
  theme: 'dark',
});</code></pre>
                </div>
            </div>
        </div>
    </x-filament::section>

    <!-- Checkout - Authenticated -->
    <x-filament::section collapsible collapsed>
        <x-slot name="heading">
            Checkout Modal - Authenticated
        </x-slot>
        <x-slot name="description">
            Test checkout flow for authenticated users.
        </x-slot>

        <div class="demo-section">
            <div>
                <div data-auth-warning class="mb-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-200 rounded-lg text-sm" style="display: none;">
                    Please login first using the Authentication section.
                </div>

                <p class="text-sm font-medium mb-2">Product Checkout:</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Email is auto-filled from token.</p>
                <div class="flex flex-wrap gap-2 mb-4">
                    <x-filament::button data-demo-action="checkout-product" data-theme="light" data-requires-auth>
                        Light
                    </x-filament::button>
                    <x-filament::button data-demo-action="checkout-product" data-theme="dark" color="gray" data-requires-auth>
                        Dark
                    </x-filament::button>
                </div>

                <p class="text-sm font-medium mb-2 mt-6">Specific Price:</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Direct checkout with authentication.</p>
                <div class="flex flex-wrap gap-2">
                    <x-filament::button data-demo-action="checkout-price" data-theme="light" data-requires-auth>
                        Light
                    </x-filament::button>
                    <x-filament::button data-demo-action="checkout-price" data-theme="dark" color="gray" data-requires-auth>
                        Dark
                    </x-filament::button>
                </div>
            </div>

            <div>
                <p class="text-sm font-medium mb-2">Code:</p>
                <div class="demo-code">
                    <button type="button" class="copy-button" data-copy-target="checkout-auth">Copy</button>
                    <pre id="checkout-auth"><code>paycan.setUserToken(token);

paycan.openCheckoutModal('{{ $productId }}', {
  theme: 'light',
  onSuccess: (checkoutUrl) => {
    window.location.href = checkoutUrl;
  },
});</code></pre>
                </div>
            </div>
        </div>
    </x-filament::section>

    <!-- Products Modal -->
    <x-filament::section collapsible collapsed>
        <x-slot name="heading">
            Products Modal
        </x-slot>
        <x-slot name="description">
            Browse all available products and plans.
        </x-slot>

        <div class="demo-section">
            <div>
                <p class="text-sm font-medium mb-2">All Products:</p>
                <div class="flex flex-wrap gap-2 mb-4">
                    <x-filament::button data-demo-action="products" data-theme="light">
                        Light
                    </x-filament::button>
                    <x-filament::button data-demo-action="products" data-theme="dark" color="gray">
                        Dark
                    </x-filament::button>
                </div>

                <p class="text-sm font-medium mb-2 mt-6">Filtered:</p>
                <div class="flex flex-wrap gap-2">
                    <x-filament::button data-demo-action="products" data-type="subscription" color="info">
                        Subscriptions
                    </x-filament::button>
                    <x-filament::button data-demo-action="products" data-type="digital" color="success">
                        Digital
                    </x-filament::button>
                </div>
            </div>

            <div>
                <p class="text-sm font-medium mb-2">Code:</p>
                <div class="demo-code">
                    <button type="button" class="copy-button" data-copy-target="products">Copy</button>
                    <pre id="products"><code>paycan.openProductsModal({
  theme: 'light',
  onProductSelected: (product) => console.log(product),
});

paycan.openProductsModal({
  type: 'subscription',
  theme: 'dark',
});</code></pre>
                </div>
            </div>
        </div>
    </x-filament::section>
    @endif

    <!-- Subscriptions -->
    <x-filament::section collapsible collapsed>
        <x-slot name="heading">
            Subscriptions Modal
        </x-slot>
        <x-slot name="description">
            View and manage user subscriptions (requires authentication).
        </x-slot>

        <div class="demo-section">
            <div>
                <div data-auth-warning class="mb-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-200 rounded-lg text-sm" style="display: none;">
                    Please login first.
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">View subscriptions with status and billing info.</p>

                <div class="flex flex-wrap gap-2">
                    <x-filament::button data-demo-action="subscriptions" data-theme="light" data-requires-auth>
                        Light
                    </x-filament::button>
                    <x-filament::button data-demo-action="subscriptions" data-theme="dark" color="gray" data-requires-auth>
                        Dark
                    </x-filament::button>
                </div>
            </div>

            <div>
                <p class="text-sm font-medium mb-2">Code:</p>
                <div class="demo-code">
                    <button type="button" class="copy-button" data-copy-target="subs">Copy</button>
                    <pre id="subs"><code>import { SubscriptionsModal } from '{{ config('app.url') }}/sdk/paycan-sdk.js';

const modal = new SubscriptionsModal(paycan, {
  theme: 'light',
});
modal.open();</code></pre>
                </div>
            </div>
        </div>
    </x-filament::section>

    <!-- Orders -->
    <x-filament::section collapsible collapsed>
        <x-slot name="heading">
            Orders Modal
        </x-slot>
        <x-slot name="description">
            View user order history (requires authentication).
        </x-slot>

        <div class="demo-section">
            <div>
                <div data-auth-warning class="mb-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-200 rounded-lg text-sm" style="display: none;">
                    Please login first.
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">View orders with payment details and downloads.</p>

                <div class="flex flex-wrap gap-2">
                    <x-filament::button data-demo-action="orders" data-theme="light" data-requires-auth>
                        Light
                    </x-filament::button>
                    <x-filament::button data-demo-action="orders" data-theme="dark" color="gray" data-requires-auth>
                        Dark
                    </x-filament::button>
                </div>
            </div>

            <div>
                <p class="text-sm font-medium mb-2">Code:</p>
                <div class="demo-code">
                    <button type="button" class="copy-button" data-copy-target="orders">Copy</button>
                    <pre id="orders"><code>import { OrdersModal } from '{{ config('app.url') }}/sdk/paycan-sdk.js';

const modal = new OrdersModal(paycan, {
  theme: 'dark',
});
modal.open();</code></pre>
                </div>
            </div>
        </div>
    </x-filament::section>

    <!-- Transactions -->
    <x-filament::section collapsible collapsed>
        <x-slot name="heading">
            Transactions Modal
        </x-slot>
        <x-slot name="description">
            View transaction history (requires authentication).
        </x-slot>

        <div class="demo-section">
            <div>
                <div data-auth-warning class="mb-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-200 rounded-lg text-sm" style="display: none;">
                    Please login first.
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">View transactions with gateway info and status.</p>

                <div class="flex flex-wrap gap-2">
                    <x-filament::button data-demo-action="transactions" data-theme="light" data-requires-auth>
                        Light
                    </x-filament::button>
                    <x-filament::button data-demo-action="transactions" data-theme="dark" color="gray" data-requires-auth>
                        Dark
                    </x-filament::button>
                </div>
            </div>

            <div>
                <p class="text-sm font-medium mb-2">Code:</p>
                <div class="demo-code">
                    <button type="button" class="copy-button" data-copy-target="transactions">Copy</button>
                    <pre id="transactions"><code>import { TransactionsModal } from '{{ config('app.url') }}/sdk/paycan-sdk.js';

const modal = new TransactionsModal(paycan, {
  theme: 'light',
});
modal.open();</code></pre>
                </div>
            </div>
        </div>
    </x-filament::section>

    <!-- API Reference -->
    <x-filament::section collapsible collapsed>
        <x-slot name="heading">
            API Reference
        </x-slot>
        <x-slot name="description">
            Common SDK methods and usage examples. Tip: <code>window.paycan</code> is exposed on this page — try these in the browser console.
        </x-slot>

        <div class="space-y-6">
            <div class="demo-section">
                <div>
                    <h3 class="text-base font-semibold mb-2">Authentication</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Set tokens, check status, logout.</p>
                </div>
                <div class="demo-code">
                    <button type="button" class="copy-button" data-copy-target="api-auth">Copy</button>
                    <pre id="api-auth"><code>paycan.setUserToken(token);
paycan.isAuthenticated(); // boolean
paycan.getToken();
paycan.logout();
const { data: user } = await paycan.me();</code></pre>
                </div>
            </div>

            <div class="demo-section">
                <div>
                    <h3 class="text-base font-semibold mb-2">Orders API</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">List orders, get downloads, licenses.</p>
                </div>
                <div class="demo-code">
                    <button type="button" class="copy-button" data-copy-target="api-orders">Copy</button>
                    <pre id="api-orders"><code>await paycan.orders.list({
  filter: { status: 'completed' },
  include: 'productPrice.product',
  sort: '-created_at',
});
await paycan.orders.get(orderId);
await paycan.orders.getDownloads(orderId);
await paycan.orders.getLicenses(orderId);</code></pre>
                </div>
            </div>

            <div class="demo-section">
                <div>
                    <h3 class="text-base font-semibold mb-2">Subscriptions API</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Manage subscriptions. <code>listActive()</code> is cached (60s) — safe to call on every page view.</p>
                </div>
                <div class="demo-code">
                    <button type="button" class="copy-button" data-copy-target="api-subs">Copy</button>
                    <pre id="api-subs"><code>await paycan.subscriptions.list({
  filter: { status: 'active' },
});
await paycan.subscriptions.listActive();
await paycan.subscriptions.get(id);
await paycan.subscriptions.cancel(id);
await paycan.subscriptions.resume(id);</code></pre>
                </div>
            </div>

            <div class="demo-section">
                <div>
                    <h3 class="text-base font-semibold mb-2">Products API</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">List and retrieve products.</p>
                </div>
                <div class="demo-code">
                    <button type="button" class="copy-button" data-copy-target="api-products">Copy</button>
                    <pre id="api-products"><code>await paycan.products.list({
  filter: { type: 'subscription' },
  include: 'prices',
});
await paycan.products.get(id);</code></pre>
                </div>
            </div>

            <div class="demo-section">
                <div>
                    <h3 class="text-base font-semibold mb-2">Checkout API</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Preview totals and create checkout sessions with your own UI.</p>
                </div>
                <div class="demo-code">
                    <button type="button" class="copy-button" data-copy-target="api-checkout">Copy</button>
                    <pre id="api-checkout"><code>const preview = await paycan.checkout.preview({
  product_id: '{{ $productId ?? 'product-id' }}',
  quantity: 2,
});

const session = await paycan.checkout.create({
  product_id: '{{ $productId ?? 'product-id' }}',
  product_price_id: '{{ $priceId ?? 'price-id' }}',
  gateway: 'stripe',
  billing_email: 'guest@example.com', // guests only
});
// then: window.location.href = session.checkout_url;</code></pre>
                </div>
            </div>

            <div class="demo-section">
                <div>
                    <h3 class="text-base font-semibold mb-2">Transactions API</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">View transactions.</p>
                </div>
                <div class="demo-code">
                    <button type="button" class="copy-button" data-copy-target="api-trans">Copy</button>
                    <pre id="api-trans"><code>await paycan.transactions.list({
  filter: { status: 'succeeded' },
  sort: '-created_at',
});
await paycan.transactions.get(id);</code></pre>
                </div>
            </div>
        </div>
    </x-filament::section>

    <script type="module">
        import { PayCan, SubscriptionsModal, OrdersModal, TransactionsModal } from '/sdk/paycan-sdk.js?v={{ filemtime(public_path('sdk/paycan-sdk.js')) }}';

        const DEMO_TOKEN = {{ Js::from($token) }};
        const PRODUCT_ID = {{ Js::from($productId) }};
        const PRICE_ID = {{ Js::from($priceId) }};

        const paycan = new PayCan({
            apiUrl: window.location.origin,
            debug: true,
        });

        window.paycan = paycan;

        function notify(title, type = 'info', body = null) {
            const notification = new FilamentNotification().title(title).duration(4000);
            if (body) {
                notification.body(body);
            }
            notification[type]().send();
        }

        function requireAuth() {
            if (paycan.isAuthenticated()) {
                return true;
            }
            notify('Please login first using the Authentication section.', 'warning');
            return false;
        }

        function syncAuthUi() {
            const isAuthed = paycan.isAuthenticated();

            const badge = document.getElementById('authStatusBadge');
            badge.className = isAuthed ? 'auth-badge authenticated' : 'auth-badge guest';
            badge.querySelector('[data-auth-label]').textContent = isAuthed ? 'Authenticated' : 'Guest Mode';

            document.getElementById('userInfoDisplay').style.display = isAuthed ? 'block' : 'none';

            document.querySelectorAll('[data-requires-auth]').forEach((el) => {
                el.classList.toggle('needs-auth', !isAuthed);
            });
            document.querySelectorAll('[data-auth-warning]').forEach((el) => {
                el.style.display = isAuthed ? 'none' : 'block';
            });
            document.querySelectorAll('[data-demo-action="login"]').forEach((el) => {
                el.classList.toggle('needs-auth', isAuthed);
            });
            document.querySelectorAll('[data-demo-action="logout"]').forEach((el) => {
                el.classList.toggle('needs-auth', !isAuthed);
            });
        }

        const modalOptions = (trigger) => ({
            theme: trigger?.dataset.theme ?? 'light',
            onError: (error) => notify(error.message ?? 'Something went wrong.', 'danger'),
        });

        const checkoutOptions = (trigger) => ({
            ...modalOptions(trigger),
            onSuccess: (checkoutUrl) => {
                console.log('[Demo] Checkout URL:', checkoutUrl);
                notify('Checkout session created', 'success', 'In production the customer is now redirected to the gateway. URL logged to the console.');
            },
            onCancel: () => notify('Checkout cancelled', 'info'),
        });

        const actions = {
            'login': () => {
                paycan.setUserToken(DEMO_TOKEN);
                syncAuthUi();
                notify('Logged in as demo user', 'success');
            },
            'logout': () => {
                if (!paycan.isAuthenticated()) {
                    notify('Already in guest mode', 'info');
                    return;
                }
                paycan.logout();
                syncAuthUi();
                notify('Logged out', 'success');
            },
            'check-status': () => {
                notify(paycan.isAuthenticated() ? 'Authenticated' : 'Guest mode', 'info');
            },
            'checkout-product': (trigger) => paycan.openCheckoutModal(PRODUCT_ID, checkoutOptions(trigger)),
            'checkout-price': (trigger) => paycan.openCheckoutModalPrice(PRICE_ID, checkoutOptions(trigger)),
            'products': (trigger) => paycan.openProductsModal({
                ...modalOptions(trigger),
                type: trigger.dataset.type || undefined,
                onProductSelected: (product) => notify('Selected: ' + (product.title ?? product.name ?? product.id), 'success'),
            }),
            'subscriptions': (trigger) => new SubscriptionsModal(paycan, modalOptions(trigger)).open(),
            'orders': (trigger) => new OrdersModal(paycan, modalOptions(trigger)).open(),
            'transactions': (trigger) => new TransactionsModal(paycan, modalOptions(trigger)).open(),
        };

        async function copyCode(button) {
            const code = document.getElementById(button.dataset.copyTarget)?.textContent ?? '';
            try {
                await navigator.clipboard.writeText(code);
                button.textContent = 'Copied!';
                button.classList.add('copied');
                setTimeout(() => {
                    button.textContent = 'Copy';
                    button.classList.remove('copied');
                }, 2000);
            } catch {
                notify('Copy failed — select the text manually.', 'danger');
            }
        }

        document.addEventListener('click', (event) => {
            const copyButton = event.target.closest('[data-copy-target]');
            if (copyButton) {
                copyCode(copyButton);
                return;
            }

            const trigger = event.target.closest('[data-demo-action]');
            if (!trigger) {
                return;
            }

            if (trigger.hasAttribute('data-requires-auth') && !requireAuth()) {
                return;
            }

            actions[trigger.dataset.demoAction]?.(trigger);
        });

        // The SDK restores its token from localStorage in the constructor. If a
        // previous visit left the demo signed in, replace that (possibly stale)
        // token with the fresh one issued for this page load.
        if (paycan.isAuthenticated()) {
            paycan.setUserToken(DEMO_TOKEN);
        }
        syncAuthUi();
    </script>
</x-filament-panels::page>
