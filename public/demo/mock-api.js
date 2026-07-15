/**
 * PayCan Demo — Mock API
 *
 * Intercepts window.fetch for PayCan API routes (/api/...) and answers with
 * in-memory mock data, so the real SDK and its web components run without a
 * backend, database, or API keys.
 *
 * This file is demo-only. It must be loaded BEFORE the SDK makes any request.
 * Inspect or tweak the dataset from the console via `window.PayCanMock.db`.
 */
(function () {
  'use strict';

  var realFetch = window.fetch.bind(window);

  var DAY = 86400000;
  var DEMO_BASE = window.location.pathname.replace(/[^/]*$/, '');

  function iso(offsetDays) {
    return new Date(Date.now() + offsetDays * DAY).toISOString();
  }

  function uid() {
    return Math.random().toString(36).slice(2, 8);
  }

  /* ------------------------------------------------------------------ *
   * Mock dataset
   * ------------------------------------------------------------------ */

  var db = {
    user: {
      id: 'usr_demo_1',
      name: 'Alex Developer',
      email: 'alex@example.com',
    },

    products: [
      {
        id: 'prod_starter',
        title: 'Starter Plan',
        slug: 'starter-plan',
        type: 'subscription',
        is_active: true,
        description: 'Everything you need to launch: 1 project, community support and core analytics.',
        prices: [
          { id: 'price_starter_monthly', title: 'Monthly', amount: 9, currency: 'USD', billing_period: 'monthly', trial_days: null },
          { id: 'price_starter_yearly', title: 'Yearly — 2 months free', amount: 90, currency: 'USD', billing_period: 'yearly', trial_days: null },
        ],
      },
      {
        id: 'prod_pro',
        title: 'Pro Plan',
        slug: 'pro-plan',
        type: 'subscription',
        is_active: true,
        description: 'For growing teams: unlimited projects, priority support, advanced analytics and webhooks.',
        prices: [
          { id: 'price_pro_monthly', title: 'Monthly', amount: 29, currency: 'USD', billing_period: 'monthly', trial_days: 14 },
          { id: 'price_pro_yearly', title: 'Yearly — 2 months free', amount: 290, currency: 'USD', billing_period: 'yearly', trial_days: 14 },
          { id: 'price_pro_lifetime', title: 'Lifetime deal', amount: 499, currency: 'USD', billing_period: 'once', trial_days: null },
        ],
      },
      {
        id: 'prod_ebook',
        title: 'The Payments Handbook (e-book)',
        slug: 'payments-handbook',
        type: 'digital',
        is_active: true,
        description: 'A 120-page practical guide to payment integrations, PDF + ePub, with a license key.',
        prices: [
          { id: 'price_ebook', title: 'Digital download', amount: 19, currency: 'USD', billing_period: 'once', trial_days: null },
        ],
      },
      {
        id: 'prod_onboarding',
        title: '1:1 Onboarding Session',
        slug: 'onboarding-session',
        type: 'service',
        is_active: true,
        description: 'A 60-minute video call with an integration engineer to get your setup production-ready.',
        prices: [
          { id: 'price_onboarding', title: 'Single session', amount: 149, currency: 'USD', billing_period: 'once', trial_days: null },
        ],
      },
    ],

    orders: [
      {
        id: 'ord_1003',
        order_number: 'ORD-2026-1003',
        status: 'pending',
        total: 149,
        currency: 'USD',
        gateway: 'stripe',
        quantity: 1,
        created_at: iso(-2),
        updated_at: iso(-2),
        product: { id: 'prod_onboarding', name: '1:1 Onboarding Session' },
        product_price: { id: 'price_onboarding', title: 'Single session', amount: 149, currency: 'USD', billing_period: 'once' },
      },
      {
        id: 'ord_1001',
        order_number: 'ORD-2026-1001',
        status: 'completed',
        total: 29,
        currency: 'USD',
        gateway: 'stripe',
        quantity: 1,
        created_at: iso(-12),
        updated_at: iso(-12),
        product: { id: 'prod_pro', name: 'Pro Plan' },
        product_price: { id: 'price_pro_monthly', title: 'Monthly', amount: 29, currency: 'USD', billing_period: 'monthly' },
      },
      {
        id: 'ord_1002',
        order_number: 'ORD-2026-1002',
        status: 'completed',
        total: 19,
        currency: 'USD',
        gateway: 'paypal',
        quantity: 1,
        created_at: iso(-30),
        updated_at: iso(-30),
        product: { id: 'prod_ebook', name: 'The Payments Handbook (e-book)' },
        product_price: { id: 'price_ebook', title: 'Digital download', amount: 19, currency: 'USD', billing_period: 'once' },
      },
    ],

    subscriptions: [
      {
        id: 'sub_2001',
        status: 'active',
        gateway: 'stripe',
        created_at: iso(-12),
        updated_at: iso(-12),
        current_period_start: iso(-12),
        current_period_end: iso(18),
        next_billing_date: iso(18),
        ends_at: null,
        canceled_at: null,
        trial_ends_at: null,
        can_resume: false,
        product: { id: 'prod_pro', name: 'Pro Plan' },
        product_price: { id: 'price_pro_monthly', title: 'Monthly', amount: 29, currency: 'USD', billing_period: 'monthly' },
      },
      {
        id: 'sub_2002',
        status: 'canceled',
        gateway: 'paypal',
        created_at: iso(-90),
        updated_at: iso(-3),
        current_period_start: iso(-24),
        current_period_end: iso(6),
        next_billing_date: null,
        ends_at: iso(6),
        canceled_at: iso(-3),
        trial_ends_at: null,
        can_resume: true,
        product: { id: 'prod_starter', name: 'Starter Plan' },
        product_price: { id: 'price_starter_monthly', title: 'Monthly', amount: 9, currency: 'USD', billing_period: 'monthly' },
      },
    ],

    transactions: [
      {
        id: 'txn_3001',
        transaction_number: 'TXN-2026-3001',
        type: 'charge',
        status: 'completed',
        amount: 29,
        currency: 'USD',
        gateway: 'stripe',
        description: 'Pro Plan — Monthly',
        created_at: iso(-12),
        order: { id: 'ord_1001', order_number: 'ORD-2026-1001' },
      },
      {
        id: 'txn_3002',
        transaction_number: 'TXN-2026-3002',
        type: 'charge',
        status: 'completed',
        amount: 19,
        currency: 'USD',
        gateway: 'paypal',
        description: 'The Payments Handbook (e-book)',
        created_at: iso(-30),
        order: { id: 'ord_1002', order_number: 'ORD-2026-1002' },
      },
      {
        id: 'txn_3003',
        transaction_number: 'TXN-2026-3003',
        type: 'refund',
        status: 'completed',
        amount: 9,
        currency: 'USD',
        gateway: 'paypal',
        description: 'Refund — Starter Plan (duplicate charge)',
        created_at: iso(-45),
        order: { id: 'ord_0999', order_number: 'ORD-2026-0999' },
      },
    ],
  };

  /* ------------------------------------------------------------------ *
   * Helpers
   * ------------------------------------------------------------------ */

  function delay(ms) {
    return new Promise(function (resolve) { setTimeout(resolve, ms); });
  }

  function json(body, status) {
    return new Response(JSON.stringify(body), {
      status: status || 200,
      headers: { 'Content-Type': 'application/json' },
    });
  }

  function paginate(items, searchParams, defaultPerPage) {
    var page = parseInt(searchParams.get('page') || '1', 10);
    var perPage = parseInt(searchParams.get('per_page') || String(defaultPerPage || 10), 10);
    var lastPage = Math.max(1, Math.ceil(items.length / perPage));
    var slice = items.slice((page - 1) * perPage, page * perPage);
    return {
      data: slice,
      links: { first: null, last: null, prev: page > 1 ? '#' : null, next: page < lastPage ? '#' : null },
      meta: {
        current_page: page,
        from: slice.length ? (page - 1) * perPage + 1 : null,
        last_page: lastPage,
        per_page: perPage,
        to: slice.length ? (page - 1) * perPage + slice.length : null,
        total: items.length,
      },
    };
  }

  function findPrice(priceId) {
    for (var i = 0; i < db.products.length; i++) {
      var product = db.products[i];
      for (var j = 0; j < product.prices.length; j++) {
        if (String(product.prices[j].id) === String(priceId)) {
          return { product: product, price: product.prices[j] };
        }
      }
    }
    return null;
  }

  function toPreviewPrice(price, quantity) {
    return {
      id: price.id,
      name: price.title,
      amount: price.amount,
      currency: price.currency,
      billing_period: price.billing_period,
      trial_days: price.trial_days,
      is_recurring: price.billing_period !== 'once',
      subtotal: price.amount * quantity,
      final_price: price.amount * quantity,
    };
  }

  var PAYMENT_METHODS = [
    { key: 'stripe', name: 'Stripe', icon: null, description: 'Pay securely with credit or debit card', supports_subscriptions: true },
    { key: 'paypal', name: 'PayPal', icon: null, description: 'Pay with your PayPal account', supports_subscriptions: true },
  ];

  function buildPreview(sp) {
    var quantity = Math.max(1, parseInt(sp.get('quantity') || '1', 10));
    var productId = sp.get('product_id');
    var priceId = sp.get('product_price_id');
    var selectedId = sp.get('selected_price_id') || priceId;

    var product = null;
    var prices = [];

    if (productId) {
      product = db.products.find(function (p) { return String(p.id) === String(productId); });
      if (product) { prices = product.prices; }
    } else if (priceId) {
      var match = findPrice(priceId);
      if (match) { product = match.product; prices = [match.price]; }
    }

    if (!product) { return null; }

    var selected = prices.find(function (p) { return String(p.id) === String(selectedId); }) || prices[0];

    return {
      product: {
        id: product.id,
        name: product.title,
        type: product.type,
        description: product.description,
        image: null,
      },
      selected_price: selected ? toPreviewPrice(selected, quantity) : null,
      prices: prices.map(function (p) { return toPreviewPrice(p, quantity); }),
      quantity: quantity,
      payment_methods: PAYMENT_METHODS,
    };
  }

  function isAuthenticated(init) {
    var headers = (init && init.headers) || {};
    var auth = headers['Authorization'] || headers['authorization'];
    return typeof auth === 'string' && auth.indexOf('Bearer ') === 0;
  }

  function unauthenticated() {
    return json({ message: 'Unauthenticated.' }, 401);
  }

  /* ------------------------------------------------------------------ *
   * Router
   * ------------------------------------------------------------------ */

  function route(method, path, sp, body, init) {
    var m;

    if (method === 'GET' && path === '/api/user/me') {
      if (!isAuthenticated(init)) { return unauthenticated(); }
      return json({ user: db.user });
    }

    if (method === 'POST' && path === '/api/auth/refresh') {
      return json({ token: 'pcn_demo_token_' + uid() });
    }

    if (method === 'GET' && path === '/api/user/products') {
      var products = db.products.filter(function (p) { return p.is_active; });
      var type = sp.get('filter[type]');
      if (type) { products = products.filter(function (p) { return p.type === type; }); }
      return json(paginate(products, sp, 12));
    }

    if ((m = path.match(/^\/api\/user\/products\/([^/]+)$/)) && method === 'GET') {
      var found = db.products.find(function (p) { return String(p.id) === String(m[1]); });
      return found ? json({ data: found }) : json({ message: 'Product not found.' }, 404);
    }

    if (method === 'GET' && path === '/api/user/checkout/preview') {
      var preview = buildPreview(sp);
      return preview ? json(preview) : json({ message: 'Product not found.' }, 404);
    }

    if (method === 'POST' && path === '/api/user/checkout') {
      var priceMatch = findPrice(body.product_price_id);
      if (!priceMatch) {
        return json({ message: 'The given data was invalid.', errors: { product_price_id: ['The selected price is invalid.'] } }, 422);
      }
      if (!isAuthenticated(init) && !body.billing_email) {
        return json({ message: 'The given data was invalid.', errors: { billing_email: ['The billing email field is required for guest checkout.'] } }, 422);
      }

      var quantity = Math.max(1, parseInt(body.quantity || 1, 10));
      var ref = uid();
      var order = {
        id: 'ord_' + ref,
        order_number: 'ORD-2026-' + ref.toUpperCase(),
        status: 'pending',
        total: priceMatch.price.amount * quantity,
        currency: priceMatch.price.currency,
        gateway: body.gateway || 'stripe',
        quantity: quantity,
        created_at: new Date().toISOString(),
        updated_at: new Date().toISOString(),
        product: { id: priceMatch.product.id, name: priceMatch.product.title },
        product_price: {
          id: priceMatch.price.id,
          title: priceMatch.price.title,
          amount: priceMatch.price.amount,
          currency: priceMatch.price.currency,
          billing_period: priceMatch.price.billing_period,
        },
      };
      db.orders.unshift(order);

      var sessionId = 'cs_demo_' + ref;
      var checkoutUrl = window.location.origin + DEMO_BASE + 'success.html'
        + '?session=' + sessionId
        + '&gateway=' + encodeURIComponent(order.gateway)
        + '&order=' + encodeURIComponent(order.order_number)
        + '&total=' + encodeURIComponent(order.total + ' ' + order.currency);

      return json({
        checkout: {
          session_id: sessionId,
          checkout_url: checkoutUrl,
          order_id: order.id,
        },
      });
    }

    if (method === 'POST' && path === '/api/user/checkout/portal') {
      if (!isAuthenticated(init)) { return unauthenticated(); }
      return json({ portal: { url: window.location.origin + DEMO_BASE + 'success.html?portal=1', session_id: 'bps_demo_' + uid() } });
    }

    if (method === 'GET' && path === '/api/user/orders') {
      if (!isAuthenticated(init)) { return unauthenticated(); }
      var orders = db.orders;
      var orderStatus = sp.get('filter[status]');
      if (orderStatus) { orders = orders.filter(function (o) { return o.status === orderStatus; }); }
      return json(paginate(orders, sp, 10));
    }

    if ((m = path.match(/^\/api\/user\/orders\/([^/]+)$/)) && method === 'GET') {
      if (!isAuthenticated(init)) { return unauthenticated(); }
      var singleOrder = db.orders.find(function (o) { return String(o.id) === String(m[1]); });
      return singleOrder ? json({ data: singleOrder }) : json({ message: 'Order not found.' }, 404);
    }

    if ((m = path.match(/^\/api\/user\/orders\/([^/]+)\/downloads$/)) && method === 'GET') {
      if (!isAuthenticated(init)) { return unauthenticated(); }
      return json({
        order_id: m[1],
        downloads: [{
          product_id: 'prod_ebook',
          product_title: 'The Payments Handbook (e-book)',
          download_url: window.location.origin + DEMO_BASE + 'success.html?download=payments-handbook.pdf',
          expires_at: iso(7),
        }],
      });
    }

    if ((m = path.match(/^\/api\/user\/orders\/([^/]+)\/licenses$/)) && method === 'GET') {
      if (!isAuthenticated(init)) { return unauthenticated(); }
      return json({
        order_id: m[1],
        licenses: [{
          product_id: 'prod_ebook',
          product_title: 'The Payments Handbook (e-book)',
          license_key: 'PAYCAN-DEMO-' + uid().toUpperCase() + '-' + uid().toUpperCase(),
          expires_at: null,
        }],
      });
    }

    if (method === 'GET' && path === '/api/user/subscriptions') {
      if (!isAuthenticated(init)) { return unauthenticated(); }
      var subs = db.subscriptions;
      var subStatus = sp.get('filter[status]');
      if (subStatus) { subs = subs.filter(function (s) { return s.status === subStatus; }); }
      return json(paginate(subs, sp, 10));
    }

    if ((m = path.match(/^\/api\/user\/subscriptions\/([^/]+)$/)) && method === 'GET') {
      if (!isAuthenticated(init)) { return unauthenticated(); }
      var singleSub = db.subscriptions.find(function (s) { return String(s.id) === String(m[1]); });
      return singleSub ? json({ data: singleSub }) : json({ message: 'Subscription not found.' }, 404);
    }

    if ((m = path.match(/^\/api\/user\/subscriptions\/([^/]+)\/cancel$/)) && method === 'POST') {
      if (!isAuthenticated(init)) { return unauthenticated(); }
      var subToCancel = db.subscriptions.find(function (s) { return String(s.id) === String(m[1]); });
      if (!subToCancel) { return json({ message: 'Subscription not found.' }, 404); }
      subToCancel.status = 'canceled';
      subToCancel.canceled_at = new Date().toISOString();
      subToCancel.ends_at = subToCancel.current_period_end || iso(30);
      subToCancel.next_billing_date = null;
      subToCancel.can_resume = true;
      return json({ subscription: subToCancel });
    }

    if ((m = path.match(/^\/api\/user\/subscriptions\/([^/]+)\/resume$/)) && method === 'POST') {
      if (!isAuthenticated(init)) { return unauthenticated(); }
      var subToResume = db.subscriptions.find(function (s) { return String(s.id) === String(m[1]); });
      if (!subToResume) { return json({ message: 'Subscription not found.' }, 404); }
      subToResume.status = 'active';
      subToResume.canceled_at = null;
      subToResume.ends_at = null;
      subToResume.next_billing_date = subToResume.current_period_end || iso(30);
      subToResume.can_resume = false;
      return json({ subscription: subToResume });
    }

    if ((m = path.match(/^\/api\/user\/subscriptions\/([^/]+)\/change$/)) && method === 'POST') {
      if (!isAuthenticated(init)) { return unauthenticated(); }
      var subToChange = db.subscriptions.find(function (s) { return String(s.id) === String(m[1]); });
      var newPrice = findPrice(body.product_price_id);
      if (!subToChange || !newPrice) { return json({ message: 'Subscription or price not found.' }, 404); }
      subToChange.product = { id: newPrice.product.id, name: newPrice.product.title };
      subToChange.product_price = {
        id: newPrice.price.id,
        title: newPrice.price.title,
        amount: newPrice.price.amount,
        currency: newPrice.price.currency,
        billing_period: newPrice.price.billing_period,
      };
      subToChange.updated_at = new Date().toISOString();
      return json({ subscription: subToChange, message: 'Subscription plan changed successfully.' });
    }

    if (method === 'GET' && path === '/api/user/transactions') {
      if (!isAuthenticated(init)) { return unauthenticated(); }
      return json(paginate(db.transactions, sp, 20));
    }

    return json({ message: 'Mock route not found: ' + method + ' ' + path }, 404);
  }

  /* ------------------------------------------------------------------ *
   * Fetch interceptor
   * ------------------------------------------------------------------ */

  window.fetch = function (input, init) {
    var url = typeof input === 'string' ? input : (input && input.url) || '';
    var parsed;
    try {
      parsed = new URL(url, window.location.origin);
    } catch (e) {
      return realFetch(input, init);
    }

    if (parsed.pathname.indexOf('/api/') !== 0) {
      return realFetch(input, init);
    }

    var method = ((init && init.method) || 'GET').toUpperCase();
    var body = {};
    if (init && typeof init.body === 'string') {
      try { body = JSON.parse(init.body); } catch (e) { body = {}; }
    }

    return delay(250 + Math.random() * 250).then(function () {
      var response = route(method, parsed.pathname, parsed.searchParams, body, init);
      console.info('[PayCan Mock] ' + method + ' ' + parsed.pathname + ' → ' + response.status);
      return response;
    });
  };

  window.PayCanMock = { db: db };

  console.info(
    '%c PayCan demo %c All /api/* requests are answered by mock-api.js — no backend involved. Explore the dataset via window.PayCanMock.db',
    'background:#4f46e5;color:#fff;border-radius:3px;padding:2px 6px;font-weight:bold;',
    'color:inherit;'
  );
})();
