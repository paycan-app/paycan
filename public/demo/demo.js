/**
 * PayCan Demo — page wiring
 *
 * Uses the real SDK build vendored at ./vendor/paycan-sdk.js (a copy of the
 * same file your PayCan instance serves at /sdk/paycan-sdk.js). All network
 * calls are answered by mock-api.js, so every button below exercises the
 * exact same code path a production integration would.
 */
import { PayCan, SubscriptionsModal, OrdersModal, TransactionsModal } from './vendor/paycan-sdk.js';

const DEMO_TOKEN = 'pcn_demo_token_do_not_use_in_production';
const SIGNED_IN_KEY = 'paycan_demo_signed_in';

const paycan = new PayCan({
  apiUrl: window.location.origin,
  debug: true,
});

// Expose for the playground and for devs poking around in the console
window.paycan = paycan;

/* ------------------------------------------------------------------ *
 * Toast + status helpers
 * ------------------------------------------------------------------ */

function toast(message, type = 'info') {
  const el = document.createElement('div');
  el.className = `demo-toast demo-toast-${type}`;
  el.textContent = message;
  document.body.appendChild(el);
  requestAnimationFrame(() => el.classList.add('visible'));
  setTimeout(() => {
    el.classList.remove('visible');
    setTimeout(() => el.remove(), 300);
  }, 3200);
}

function showCheckoutResult(checkoutUrl) {
  const panel = document.getElementById('checkout-result');
  const link = document.getElementById('checkout-result-url');
  link.href = checkoutUrl;
  link.textContent = checkoutUrl;
  panel.hidden = false;
  panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

/* ------------------------------------------------------------------ *
 * Demo auth state (signed-in toggle)
 * ------------------------------------------------------------------ */

const signedInToggle = document.getElementById('signed-in-toggle');

function isSignedIn() {
  return localStorage.getItem(SIGNED_IN_KEY) !== '0';
}

function applyAuthState(signedIn, silent = false) {
  if (signedIn) {
    paycan.setUserToken(DEMO_TOKEN);
    if (!silent) toast('Signed in as Alex Developer (demo user)', 'success');
  } else {
    paycan.logout();
    if (!silent) toast('Signed out — try the checkout modal to see guest checkout', 'info');
  }
  localStorage.setItem(SIGNED_IN_KEY, signedIn ? '1' : '0');
  document.querySelectorAll('[data-requires-auth]').forEach((btn) => {
    btn.classList.toggle('needs-auth', !signedIn);
  });
}

signedInToggle.checked = isSignedIn();
applyAuthState(signedInToggle.checked, true);
signedInToggle.addEventListener('change', () => applyAuthState(signedInToggle.checked));

function requireAuth() {
  if (paycan.isAuthenticated()) return true;
  toast('This component needs a signed-in user — flip the "Signed in" switch above', 'error');
  return false;
}

/* ------------------------------------------------------------------ *
 * Theme selector
 * ------------------------------------------------------------------ */

const themeSelect = document.getElementById('theme-select');

function theme() {
  return themeSelect.value;
}

/* ------------------------------------------------------------------ *
 * Component demo buttons
 * ------------------------------------------------------------------ */

const modalOptions = () => ({
  theme: theme(),
  onError: (error) => toast(error.message || 'Something went wrong', 'error'),
});

document.getElementById('btn-checkout-product').addEventListener('click', () => {
  paycan.openCheckoutModal('prod_pro', {
    ...modalOptions(),
    onSuccess: (checkoutUrl) => showCheckoutResult(checkoutUrl),
    onCancel: () => toast('Checkout cancelled', 'info'),
  });
});

document.getElementById('btn-checkout-price').addEventListener('click', () => {
  paycan.openCheckoutModalPrice('price_ebook', {
    ...modalOptions(),
    onSuccess: (checkoutUrl) => showCheckoutResult(checkoutUrl),
  });
});

document.getElementById('btn-products').addEventListener('click', () => {
  paycan.openProductsModal({
    ...modalOptions(),
    onProductSelected: (product) => toast(`Selected: ${product.title}`, 'success'),
  });
});

document.getElementById('btn-products-subscriptions').addEventListener('click', () => {
  paycan.openProductsModal({ ...modalOptions(), type: 'subscription' });
});

document.getElementById('btn-subscriptions').addEventListener('click', () => {
  if (!requireAuth()) return;
  new SubscriptionsModal(paycan, modalOptions()).open();
});

document.getElementById('btn-orders').addEventListener('click', () => {
  if (!requireAuth()) return;
  new OrdersModal(paycan, modalOptions()).open();
});

document.getElementById('btn-transactions').addEventListener('click', () => {
  if (!requireAuth()) return;
  new TransactionsModal(paycan, modalOptions()).open();
});

/* ------------------------------------------------------------------ *
 * API playground
 * ------------------------------------------------------------------ */

const snippets = {
  'list-products': () => paycan.products.list({ include: 'prices' }),
  'list-orders': () => paycan.orders.list({ per_page: 5, sort: '-created_at' }),
  'active-subscriptions': () => paycan.subscriptions.listActive(),
  'checkout-preview': () => paycan.checkout.preview({ product_id: 'prod_pro', quantity: 2 }),
  'create-checkout': () => paycan.checkout.create({
    product_id: 'prod_ebook',
    product_price_id: 'price_ebook',
    gateway: 'stripe',
    billing_email: 'guest@example.com',
  }),
};

const output = document.getElementById('playground-output');

document.querySelectorAll('[data-snippet]').forEach((button) => {
  button.addEventListener('click', async () => {
    const key = button.dataset.snippet;

    document.querySelectorAll('[data-snippet]').forEach((b) => b.classList.remove('active'));
    button.classList.add('active');
    document.querySelectorAll('[data-snippet-code]').forEach((block) => {
      block.hidden = block.dataset.snippetCode !== key;
    });

    output.textContent = '// Running…';
    output.classList.remove('error');
    try {
      const result = await snippets[key]();
      output.textContent = JSON.stringify(result, null, 2);
    } catch (error) {
      output.classList.add('error');
      output.textContent = '// ' + (error.message || 'Request failed') +
        (error.errors ? '\n' + JSON.stringify(error.errors, null, 2) : '');
    }
  });
});

/* ------------------------------------------------------------------ *
 * Tabs (backend framework examples)
 * ------------------------------------------------------------------ */

document.querySelectorAll('[data-tabs]').forEach((group) => {
  group.querySelectorAll('.tab-btn').forEach((button) => {
    button.addEventListener('click', () => {
      group.querySelectorAll('.tab-btn').forEach((b) => b.classList.toggle('active', b === button));
      group.querySelectorAll('[data-tab-panel]').forEach((panel) => {
        panel.hidden = panel.dataset.tabPanel !== button.dataset.tab;
      });
    });
  });
});

/* ------------------------------------------------------------------ *
 * Check access — did this user buy this product?
 * ------------------------------------------------------------------ */

const accessSelect = document.getElementById('access-product-select');
const accessResult = document.getElementById('access-result');

function setAccessResult(state, message) {
  accessResult.hidden = false;
  accessResult.className = `access-result ${state}`;
  accessResult.textContent = message;
}

document.getElementById('btn-check-access').addEventListener('click', async () => {
  if (!requireAuth()) return;

  const productId = accessSelect.value;
  setAccessResult('', 'Checking…');

  try {
    const [{ data: orders }, activePlans] = await Promise.all([
      paycan.orders.list({ per_page: 50, include: 'product,productPrice' }),
      paycan.subscriptions.listActive(),
    ]);

    const activeSub = activePlans.find((sub) => sub.product?.id === productId);
    if (activeSub) {
      setAccessResult('granted', `✓ Access granted — active subscription (${activeSub.product_price.title}, ${activeSub.product_price.billing_period}).`);
      return;
    }

    const productOrders = orders.filter((order) => order.product?.id === productId);
    const completedOrder = productOrders.find((order) => order.status === 'completed');
    if (completedOrder) {
      setAccessResult('granted', `✓ Access granted — purchased via order ${completedOrder.order_number}.`);
      return;
    }

    const pendingOrder = productOrders.find((order) => order.status === 'pending');
    if (pendingOrder) {
      setAccessResult('pending', `⏳ Order ${pendingOrder.order_number} is pending — access unlocks once payment completes.`);
      return;
    }

    setAccessResult('denied', '✗ No access — no completed order or active subscription for this product.');
  } catch (error) {
    setAccessResult('denied', error.message || 'Request failed.');
  }
});

/* ------------------------------------------------------------------ *
 * Copy buttons for code blocks
 * ------------------------------------------------------------------ */

document.querySelectorAll('.code-block').forEach((block) => {
  const button = document.createElement('button');
  button.type = 'button';
  button.className = 'copy-btn';
  button.textContent = 'Copy';
  button.addEventListener('click', async () => {
    const code = block.querySelector('code');
    try {
      await navigator.clipboard.writeText(code.textContent);
      button.textContent = 'Copied!';
      button.classList.add('copied');
      setTimeout(() => {
        button.textContent = 'Copy';
        button.classList.remove('copied');
      }, 1500);
    } catch {
      toast('Copy failed — select the text manually', 'error');
    }
  });
  block.appendChild(button);
});
