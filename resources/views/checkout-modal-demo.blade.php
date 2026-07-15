<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayCan Checkout Modal Demo</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f3f4f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07), 0 10px 20px rgba(0, 0, 0, 0.05);
            padding: 3rem;
            max-width: 800px;
            width: 100%;
        }

        h1 {
            font-size: 2.5rem;
            color: #1f2937;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .subtitle {
            text-align: center;
            color: #6b7280;
            margin-bottom: 3rem;
            font-size: 1.125rem;
        }

        .demo-section {
            margin-bottom: 2.5rem;
            padding: 2rem;
            background: #fafbfc;
            border-radius: 12px;
            border: 1px solid #e1e4e8;
        }

        .demo-section h2 {
            font-size: 1.5rem;
            color: #1f2937;
            margin-bottom: 1rem;
        }

        .demo-section p {
            color: #6b7280;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .code-block {
            background: #24292e;
            color: #e1e4e8;
            padding: 1rem;
            border-radius: 8px;
            font-family: 'Monaco', 'Menlo', 'Consolas', monospace;
            font-size: 0.875rem;
            overflow-x: auto;
            margin-bottom: 1.5rem;
            border: 1px solid #1b1f23;
        }

        .button-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        button {
            background: #2563eb;
            color: white;
            border: none;
            padding: 0.875rem 1.75rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        button:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
        }

        button:active {
            transform: translateY(0);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        button.dark {
            background: #1f2937;
        }

        button.dark:hover {
            background: #111827;
        }

        .status {
            margin-top: 2rem;
            padding: 1rem;
            border-radius: 8px;
            background: #f0fdf4;
            border: 1px solid #86efac;
            color: #166534;
            display: none;
        }

        .status.error {
            background: #fef2f2;
            border-color: #fca5a5;
            color: #991b1b;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .feature {
            padding: 1.5rem;
            background: white;
            border-radius: 8px;
            border: 1px solid #e1e4e8;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .feature:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .feature h3 {
            color: #1f2937;
            margin-bottom: 0.5rem;
            font-size: 1.125rem;
        }

        .feature p {
            color: #6b7280;
            font-size: 0.875rem;
            margin: 0;
        }

        @media (max-width: 640px) {
            .container {
                padding: 1.5rem;
            }

            h1 {
                font-size: 1.875rem;
            }

            .button-group {
                flex-direction: column;
            }

            button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛒 PayCan Checkout Modal</h1>
        <p class="subtitle">Web Component Demo - Framework Agnostic</p>

        <div class="demo-section">
            <h2>Product Checkout (Multiple Prices)</h2>
            <p>Open checkout modal for a product with multiple price options. Users can select their preferred plan.</p>
            <div class="code-block">paycan.openCheckoutModal('{{ $productId }}', { theme: 'auto' });</div>
            <div class="button-group">
                <button id="btn-product-light">Light Theme</button>
                <button id="btn-product-dark" class="dark">Dark Theme</button>
                <button id="btn-product-auto" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">Auto Theme</button>
            </div>
        </div>

        <div class="demo-section">
            <h2>Price Checkout (Specific Price)</h2>
            <p>Open checkout modal for a specific price. Ideal when you know exactly which plan the user wants.</p>
            <div class="code-block">paycan.openCheckoutModalPrice('{{ $priceId }}', { theme: 'auto' });</div>
            <div class="button-group">
                <button id="btn-price-light">Light Theme</button>
                <button id="btn-price-dark" class="dark">Dark Theme</button>
                <button id="btn-price-auto" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">Auto Theme</button>
            </div>
        </div>

        <div class="demo-section">
            <h2>Guest Checkout Test</h2>
            <p>Test checkout without authentication. The modal will display an email field for guest users.</p>
            <div class="code-block">// Without authentication - guest checkout
paycan.openCheckoutModal('{{ $productId }}');</div>
            <div class="button-group">
                <button id="btn-guest">Test Guest Checkout</button>
            </div>
        </div>

        <div class="demo-section">
            <h2>Products Modal (Browse & Buy)</h2>
            <p>Browse all available products in a modal. When a product is selected, it opens the checkout modal automatically.</p>
            <div class="code-block">paycan.openProductsModal({ theme: 'auto' });</div>
            <div class="button-group">
                <button id="btn-products-light">All Products (Light)</button>
                <button id="btn-products-dark" class="dark">All Products (Dark)</button>
                <button id="btn-products-auto" style="background: #f59e0b;">All Products (Auto)</button>
            </div>
            <div class="button-group" style="margin-top: 1rem;">
                <button id="btn-products-subscription" style="background: #8b5cf6;">Subscriptions</button>
                <button id="btn-products-digital" style="background: #10b981;">Digital Products</button>
            </div>
        </div>

        <div class="features">
            <div class="feature">
                <h3>✨ Auto Price Selection</h3>
                <p>Radio buttons for < 3 prices, dropdown for ≥ 3 prices</p>
            </div>
            <div class="feature">
                <h3>💳 Dynamic Gateways</h3>
                <p>Shows available payment methods from preview API</p>
            </div>
            <div class="feature">
                <h3>📧 Guest Support</h3>
                <p>Email field appears for unauthenticated users</p>
            </div>
            <div class="feature">
                <h3>🌓 Theme Support</h3>
                <p>Light, dark, and auto mode (system preference)</p>
            </div>
            <div class="feature">
                <h3>📱 Fully Responsive</h3>
                <p>Works on mobile, tablet, and desktop</p>
            </div>
            <div class="feature">
                <h3>🔒 Secure</h3>
                <p>Input sanitization and XSS prevention</p>
            </div>
        </div>

        <div id="status-message"></div>
    </div>

    <!-- Load PayCan SDK from public directory -->
    <script type="module">
        import { PayCan } from '/sdk/paycan-sdk.js';

        // Initialize SDK
        const paycan = new PayCan({
            apiUrl: window.location.origin,
            debug: true
        });

        // Helper function to show status
        function showStatus(message, isError) {
            const statusDiv = document.getElementById('status-message');
            statusDiv.textContent = message;
            statusDiv.className = 'status' + (isError ? ' error' : '');
            statusDiv.style.display = 'block';

            if (!message.includes('...')) {
                setTimeout(() => {
                    statusDiv.style.display = 'none';
                }, 5000);
            }
        }

        // Function to open product checkout
        function openProductCheckout(theme) {
            showStatus('Opening product checkout with ' + theme + ' theme...', false);

            paycan.openCheckoutModal('{{ $productId ?? "null" }}', {
                theme: theme,
                onSuccess: (checkoutUrl) => {
                    showStatus('✓ Checkout session created! Redirecting...', false);
                    console.log('Checkout URL:', checkoutUrl);
                    // Redirect to checkout page
                    setTimeout(() => {
                        window.location.href = checkoutUrl;
                    }, 1500);
                },
                onCancel: () => {
                    showStatus('Checkout cancelled by user', false);
                },
                onError: (error) => {
                    showStatus('✗ Error: ' + error.message, true);
                }
            });
        }

        // Function to open price checkout
        function openPriceCheckout(theme) {
            showStatus('Opening price checkout with ' + theme + ' theme...', false);

            paycan.openCheckoutModalPrice('{{ $priceId ?? "null" }}', {
                theme: theme,
                onSuccess: (checkoutUrl) => {
                    showStatus('✓ Checkout session created! Redirecting...', false);
                    console.log('Checkout URL:', checkoutUrl);
                    // Redirect to checkout page
                    setTimeout(() => {
                        window.location.href = checkoutUrl;
                    }, 1500);
                },
                onCancel: () => {
                    showStatus('Checkout cancelled by user', false);
                },
                onError: (error) => {
                    showStatus('✗ Error: ' + error.message, true);
                }
            });
        }

        // Function to open guest checkout
        function openGuestCheckout() {
            showStatus('Opening guest checkout (email field will be shown)...', false);

            paycan.openCheckoutModal('{{ $productId ?? "null" }}', {
                theme: 'light',
                onSuccess: (checkoutUrl) => {
                    showStatus('✓ Guest checkout session created!', false);
                    console.log('Checkout URL:', checkoutUrl);
                    setTimeout(() => {
                        window.location.href = checkoutUrl;
                    }, 1500);
                },
                onCancel: () => {
                    showStatus('Guest checkout cancelled', false);
                },
                onError: (error) => {
                    showStatus('✗ Error: ' + error.message, true);
                }
            });
        }

        // Function to open products modal
        function openProductsModal(type = null, theme = 'auto') {
            const options = { theme };
            if (type) {
                options.type = type;
                showStatus(`Opening ${type} products modal...`, false);
            } else {
                showStatus('Opening products modal...', false);
            }

            paycan.openProductsModal({
                ...options,
                onProductSelected: (product) => {
                    showStatus('Product selected: ' + product.title, false);
                },
                onClose: () => {
                    showStatus('Products modal closed', false);
                },
                onError: (error) => {
                    showStatus('✗ Error: ' + error.message, true);
                }
            });
        }

        // Attach event listeners to buttons
        document.getElementById('btn-product-light').addEventListener('click', () => openProductCheckout('light'));
        document.getElementById('btn-product-dark').addEventListener('click', () => openProductCheckout('dark'));
        document.getElementById('btn-product-auto').addEventListener('click', () => openProductCheckout('auto'));
        document.getElementById('btn-price-light').addEventListener('click', () => openPriceCheckout('light'));
        document.getElementById('btn-price-dark').addEventListener('click', () => openPriceCheckout('dark'));
        document.getElementById('btn-price-auto').addEventListener('click', () => openPriceCheckout('auto'));
        document.getElementById('btn-guest').addEventListener('click', () => openGuestCheckout());

        // Products modal buttons
        document.getElementById('btn-products-light').addEventListener('click', () => openProductsModal(null, 'light'));
        document.getElementById('btn-products-dark').addEventListener('click', () => openProductsModal(null, 'dark'));
        document.getElementById('btn-products-auto').addEventListener('click', () => openProductsModal(null, 'auto'));
        document.getElementById('btn-products-subscription').addEventListener('click', () => openProductsModal('subscription', 'auto'));
        document.getElementById('btn-products-digital').addEventListener('click', () => openProductsModal('digital', 'auto'));

        console.log('✓ PayCan SDK loaded and ready!');
        console.log('Authenticated:', paycan.isAuthenticated());
        console.log('Product ID:', '{{ $productId ?? "null" }}');
        console.log('Price ID:', '{{ $priceId ?? "null" }}');
    </script>
</body>
</html>
