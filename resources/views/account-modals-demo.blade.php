<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayCan Account Modals Demo</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 3rem;
            max-width: 900px;
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

        .auth-info {
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 2rem;
            color: #166534;
        }

        .auth-info strong {
            font-weight: 600;
        }

        .demo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .demo-card {
            background: #f9fafb;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 2rem;
            transition: all 0.2s;
        }

        .demo-card:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }

        .demo-card-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .demo-card h2 {
            font-size: 1.25rem;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .demo-card p {
            color: #6b7280;
            margin-bottom: 1.5rem;
            line-height: 1.6;
            font-size: 0.875rem;
        }

        button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        button:active {
            transform: translateY(0);
        }

        button.secondary {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        }

        button.accent {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .feature {
            padding: 1rem;
            background: #f9fafb;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .feature h3 {
            color: #1f2937;
            margin-bottom: 0.25rem;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .feature p {
            color: #6b7280;
            font-size: 0.75rem;
            margin: 0;
        }

        .theme-switcher {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .theme-switcher button {
            flex: 1;
            padding: 0.5rem;
            font-size: 0.75rem;
        }

        .status {
            margin-top: 2rem;
            padding: 1rem;
            border-radius: 8px;
            background: #f0fdf4;
            border: 1px solid #86efac;
            color: #166534;
            display: none;
            font-size: 0.875rem;
        }

        .status.error {
            background: #fef2f2;
            border-color: #fca5a5;
            color: #991b1b;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1.5rem;
            }

            h1 {
                font-size: 1.875rem;
            }

            .demo-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 PayCan Account Modals</h1>
        <p class="subtitle">Web Components Demo - Subscriptions, Orders & Transactions</p>

        <div class="auth-info">
            <strong>✓ Authenticated as:</strong> {{ $user->name }} ({{ $user->email }})
        </div>

        <div class="demo-grid">
            <!-- Subscriptions Modal -->
            <div class="demo-card">
                <div class="demo-card-icon">🔄</div>
                <h2>Subscriptions</h2>
                <p>View and manage all user subscriptions. Cancel or resume subscriptions, and view transaction history.</p>
                <button id="btn-subscriptions-light">Open (Light)</button>
                <div class="theme-switcher">
                    <button id="btn-subscriptions-dark" class="secondary">Dark</button>
                    <button id="btn-subscriptions-auto" class="accent">Auto</button>
                </div>
            </div>

            <!-- Orders Modal -->
            <div class="demo-card">
                <div class="demo-card-icon">🛒</div>
                <h2>Orders</h2>
                <p>Browse all user orders with detailed information including payment status, amounts, and transaction history.</p>
                <button id="btn-orders-light">Open (Light)</button>
                <div class="theme-switcher">
                    <button id="btn-orders-dark" class="secondary">Dark</button>
                    <button id="btn-orders-auto" class="accent">Auto</button>
                </div>
            </div>

            <!-- Transactions Modal -->
            <div class="demo-card">
                <div class="demo-card-icon">💳</div>
                <h2>Transactions</h2>
                <p>Complete transaction history with payment details, status, and related order information.</p>
                <button id="btn-transactions-light">Open (Light)</button>
                <div class="theme-switcher">
                    <button id="btn-transactions-dark" class="secondary">Dark</button>
                    <button id="btn-transactions-auto" class="accent">Auto</button>
                </div>
            </div>
        </div>

        <div class="features">
            <div class="feature">
                <h3>🎨 Shadow DOM</h3>
                <p>Complete style isolation</p>
            </div>
            <div class="feature">
                <h3>📱 Responsive</h3>
                <p>Mobile-friendly design</p>
            </div>
            <div class="feature">
                <h3>🌓 Theme Support</h3>
                <p>Light, dark, and auto</p>
            </div>
            <div class="feature">
                <h3>📄 Pagination</h3>
                <p>Navigate large datasets</p>
            </div>
            <div class="feature">
                <h3>🔍 Transaction History</h3>
                <p>Embedded in subscriptions & orders</p>
            </div>
            <div class="feature">
                <h3>⚡ Fast Loading</h3>
                <p>Optimized performance</p>
            </div>
        </div>

        <div id="status-message"></div>
    </div>

    <!-- Load PayCan SDK from public directory -->
    <script type="module">
        import { PayCan, SubscriptionsModal, OrdersModal, TransactionsModal } from '/sdk/paycan-sdk.js?v={{ now()->timestamp }}';

        // Initialize SDK
        const paycan = new PayCan({
            apiUrl: '{{ config("app.url") }}',
            debug: true
        });

        // Set the demo user token
        paycan.setUserToken('{{ $token }}');

        // Helper function to show status
        function showStatus(message, isError) {
            const statusDiv = document.getElementById('status-message');
            statusDiv.textContent = message;
            statusDiv.className = 'status' + (isError ? ' error' : '');
            statusDiv.style.display = 'block';

            setTimeout(() => {
                statusDiv.style.display = 'none';
            }, 5000);
        }

        // Function to open subscriptions modal
        function openSubscriptionsModal(theme) {
            showStatus('Opening subscriptions modal with ' + theme + ' theme...', false);

            const modal = new SubscriptionsModal(paycan, {
                theme: theme,
                onClose: () => {
                    showStatus('Subscriptions modal closed', false);
                },
                onError: (error) => {
                    showStatus('✗ Error: ' + error.message, true);
                }
            });

            modal.open();
        }

        // Function to open orders modal
        function openOrdersModal(theme) {
            showStatus('Opening orders modal with ' + theme + ' theme...', false);

            const modal = new OrdersModal(paycan, {
                theme: theme,
                onClose: () => {
                    showStatus('Orders modal closed', false);
                },
                onError: (error) => {
                    showStatus('✗ Error: ' + error.message, true);
                }
            });

            modal.open();
        }

        // Function to open transactions modal
        function openTransactionsModal(theme) {
            showStatus('Opening transactions modal with ' + theme + ' theme...', false);

            const modal = new TransactionsModal(paycan, {
                theme: theme,
                onClose: () => {
                    showStatus('Transactions modal closed', false);
                },
                onError: (error) => {
                    showStatus('✗ Error: ' + error.message, true);
                }
            });

            modal.open();
        }

        // Attach event listeners for Subscriptions
        document.getElementById('btn-subscriptions-light').addEventListener('click', () => openSubscriptionsModal('light'));
        document.getElementById('btn-subscriptions-dark').addEventListener('click', () => openSubscriptionsModal('dark'));
        document.getElementById('btn-subscriptions-auto').addEventListener('click', () => openSubscriptionsModal('auto'));

        // Attach event listeners for Orders
        document.getElementById('btn-orders-light').addEventListener('click', () => openOrdersModal('light'));
        document.getElementById('btn-orders-dark').addEventListener('click', () => openOrdersModal('dark'));
        document.getElementById('btn-orders-auto').addEventListener('click', () => openOrdersModal('auto'));

        // Attach event listeners for Transactions
        document.getElementById('btn-transactions-light').addEventListener('click', () => openTransactionsModal('light'));
        document.getElementById('btn-transactions-dark').addEventListener('click', () => openTransactionsModal('dark'));
        document.getElementById('btn-transactions-auto').addEventListener('click', () => openTransactionsModal('auto'));

        console.log('✓ PayCan SDK loaded and ready!');
        console.log('Authenticated:', paycan.isAuthenticated());
        console.log('User:', '{{ $user->name }}');
        console.log('Token set:', !!paycan.getToken());
    </script>
</body>
</html>
