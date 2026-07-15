'use strict';

Object.defineProperty(exports, '__esModule', { value: true });

/**
 * HTTP Client with automatic token management
 */
class HttpClient {
    constructor(config) {
        this.token = null;
        this.tokenExpiry = null;
        this.refreshPromise = null;
        this.TOKEN_KEY = 'paycan_auth_token';
        this.config = config;
        this.loadTokenFromStorage();
    }
    /**
     * Load token from localStorage on initialization
     */
    loadTokenFromStorage() {
        if (typeof window === 'undefined' || typeof localStorage === 'undefined') {
            return;
        }
        try {
            const storedToken = localStorage.getItem(this.TOKEN_KEY);
            if (storedToken) {
                this.token = storedToken;
                this.tokenExpiry = this.parseTokenExpiry(storedToken);
                this.log('Token loaded from storage');
            }
        }
        catch (error) {
            // Silently fail if localStorage is unavailable
            this.log('Failed to load token from storage:', error);
        }
    }
    /**
     * Save token to localStorage
     */
    saveTokenToStorage(token) {
        if (typeof window === 'undefined' || typeof localStorage === 'undefined') {
            return;
        }
        try {
            localStorage.setItem(this.TOKEN_KEY, token);
        }
        catch (error) {
            // Silently fail if localStorage quota exceeded or unavailable
            this.log('Failed to save token to storage:', error);
        }
    }
    /**
     * Remove token from localStorage
     */
    removeTokenFromStorage() {
        if (typeof window === 'undefined' || typeof localStorage === 'undefined') {
            return;
        }
        try {
            localStorage.removeItem(this.TOKEN_KEY);
        }
        catch (error) {
            // Silently fail if localStorage is unavailable
            this.log('Failed to remove token from storage:', error);
        }
    }
    /**
     * Set the authentication token
     */
    setToken(token) {
        this.token = token;
        this.tokenExpiry = this.parseTokenExpiry(token);
        this.saveTokenToStorage(token);
        if (this.tokenExpiry) {
            this.log('Token set, expires at:', new Date(this.tokenExpiry).toISOString());
        }
        else {
            this.log('Token set (no expiry - non-JWT token)');
        }
    }
    /**
     * Get current token
     */
    getToken() {
        return this.token;
    }
    /**
     * Clear the authentication token
     */
    clearToken() {
        this.token = null;
        this.tokenExpiry = null;
        this.removeTokenFromStorage();
        this.log('Token cleared');
    }
    /**
     * Check if token needs refresh
     */
    needsRefresh() {
        if (!this.tokenExpiry)
            return false;
        const threshold = (this.config.refreshThreshold || 300) * 1000; // Convert to ms
        const now = Date.now();
        const timeUntilExpiry = this.tokenExpiry - now;
        return timeUntilExpiry <= threshold;
    }
    /**
     * Parse JWT token to get expiry time
     * Note: Sanctum tokens are not JWTs and don't contain expiry info
     */
    parseTokenExpiry(token) {
        try {
            // Check if this is a JWT (has 3 parts separated by dots)
            const parts = token.split('.');
            if (parts.length !== 3) {
                // Not a JWT (likely a Sanctum token), skip parsing
                return null;
            }
            const payload = parts[1];
            // Validate that the payload is valid base64 before attempting to decode
            if (!/^[A-Za-z0-9_-]+$/.test(payload)) {
                // Not valid base64url, likely not a JWT
                return null;
            }
            const decoded = JSON.parse(atob(payload));
            return decoded.exp ? decoded.exp * 1000 : null; // Convert to ms
        }
        catch (error) {
            // Token is not a valid JWT, likely a Sanctum token
            this.log('Failed to parse token expiry:', error instanceof Error ? error.message : error);
            return null;
        }
    }
    /**
     * Make HTTP request with automatic token refresh
     */
    async request(endpoint, options = {}, skipAuthCheck = false) {
        if (!skipAuthCheck) {
            await this.ensureValidToken();
        }
        const url = `${this.config.apiUrl}${endpoint}`;
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...(options.headers || {}),
        };
        if (this.token && !skipAuthCheck) {
            headers['Authorization'] = `Bearer ${this.token}`;
        }
        this.log(`${options.method || 'GET'} ${url}`);
        const response = await fetch(url, { ...options, headers });
        // Any 401 on an auth-required request invalidates the session
        if (response.status === 401 && !skipAuthCheck) {
            this.clearToken();
            throw {
                message: 'Session expired. Please log in again.',
                status: 401,
            };
        }
        return this.handleResponse(response);
    }
    /**
     * Ensure we have a valid token, refresh if needed
     */
    async ensureValidToken() {
        if (!this.token) {
            throw new Error('Not authenticated. Please call setUserToken() first.');
        }
        if (this.isExpired()) {
            this.clearToken();
            throw { message: 'Session expired. Please log in again.', status: 401 };
        }
        // Move refresh logic into the method (remove the stray top-level block)
        if (this.needsRefresh()) {
            // If a refresh is already in progress, wait for it
            if (this.refreshPromise) {
                await this.refreshPromise;
                return;
            }
            // Start refresh
            this.refreshPromise = this.refreshToken();
            await this.refreshPromise;
            this.refreshPromise = null;
        }
    }
    /**
     * Refresh the authentication token
     * This is called automatically when token is close to expiry
     */
    async refreshToken() {
        if (!this.token) {
            throw new Error('No token to refresh');
        }
        const url = `${this.config.apiUrl}/api/auth/refresh`;
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${this.token}`,
        };
        this.log('Refreshing token...');
        const response = await fetch(url, { method: 'POST', headers });
        if (!response.ok) {
            const contentType = response.headers.get('content-type');
            if (contentType?.includes('application/json')) {
                try {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Token refresh failed');
                }
                catch {
                    throw new Error('Token refresh failed');
                }
            }
            throw new Error('Token refresh failed');
        }
        const data = await response.json();
        const newToken = data.token;
        if (!newToken) {
            throw new Error('No token received from refresh endpoint');
        }
        this.setToken(newToken);
        this.log('Token refreshed successfully');
    }
    /**
     * Handle HTTP response
     */
    async handleResponse(response) {
        const contentType = response.headers.get('content-type');
        const isJson = contentType?.includes('application/json');
        if (!response.ok) {
            let error = {
                message: response.statusText,
                status: response.status,
            };
            if (isJson) {
                try {
                    const errorData = await response.json();
                    error = {
                        message: errorData.message || error.message,
                        errors: errorData.errors,
                        status: response.status,
                    };
                }
                catch (e) {
                    // Use default error if JSON parsing fails
                }
            }
            this.log('API Error:', error);
            throw error;
        }
        if (isJson) {
            return response.json();
        }
        return {};
    }
    /**
     * Debug logging
     */
    log(...args) {
        if (this.config.debug) {
            console.log('[PayCan SDK]', ...args);
        }
    }
    /**
     * GET request
     */
    async get(endpoint, params, skipAuthCheck = false) {
        let url = endpoint;
        if (params) {
            const queryString = new URLSearchParams(Object.entries(params).reduce((acc, [key, value]) => {
                if (value !== undefined && value !== null) {
                    acc[key] = String(value);
                }
                return acc;
            }, {})).toString();
            if (queryString) {
                url = `${endpoint}?${queryString}`;
            }
        }
        return this.request(url, { method: 'GET' }, skipAuthCheck);
    }
    /**
     * POST request
     */
    async post(endpoint, data, skipAuthCheck = false) {
        return this.request(endpoint, {
            method: 'POST',
            body: data ? JSON.stringify(data) : undefined,
        }, skipAuthCheck);
    }
    /**
     * PUT request
     */
    async put(endpoint, data) {
        return this.request(endpoint, {
            method: 'PUT',
            body: data ? JSON.stringify(data) : undefined,
        });
    }
    /**
     * DELETE request
     */
    async delete(endpoint) {
        return this.request(endpoint, { method: 'DELETE' });
    }
    isExpired() {
        if (!this.tokenExpiry)
            return false;
        return Date.now() >= this.tokenExpiry;
    }
}

/**
 * Orders Resource
 *
 * Handle order-related operations
 */
class Orders {
    constructor(http, config) {
        this.http = http;
        this.ordersCache = null;
        this.cacheTimestamp = null;
        this.CACHE_KEY = 'paycan_orders_cache';
        this.CACHE_TS_KEY = 'paycan_orders_cache_timestamp';
        const cacheTTLSeconds = config.cacheTtl ?? 60;
        this.cacheTtl = cacheTTLSeconds * 1000; // Convert to milliseconds
        // Load cache from localStorage on initialization
        this.loadCacheFromStorage();
    }
    /**
     * Load cache from localStorage
     */
    loadCacheFromStorage() {
        if (typeof window === 'undefined' || typeof localStorage === 'undefined') {
            return;
        }
        try {
            const cached = localStorage.getItem(this.CACHE_KEY);
            const timestamp = localStorage.getItem(this.CACHE_TS_KEY);
            if (cached && timestamp) {
                const ts = parseInt(timestamp, 10);
                if (Date.now() - ts < this.cacheTtl) {
                    this.ordersCache = JSON.parse(cached);
                    this.cacheTimestamp = ts;
                }
                else {
                    this.clearCache();
                }
            }
        }
        catch (error) {
            this.clearCache();
        }
    }
    /**
     * Save cache to localStorage
     */
    saveCacheToStorage() {
        if (typeof window === 'undefined' || typeof localStorage === 'undefined') {
            return;
        }
        try {
            if (this.ordersCache && this.cacheTimestamp) {
                localStorage.setItem(this.CACHE_KEY, JSON.stringify(this.ordersCache));
                localStorage.setItem(this.CACHE_TS_KEY, this.cacheTimestamp.toString());
            }
        }
        catch (error) {
            // Silently fail if localStorage quota exceeded or unavailable
        }
    }
    /**
     * Clear the orders cache
     *
     * Note: Cache is automatically cleared when creating new orders or completing checkouts
     * to ensure fresh data is fetched on next request.
     */
    clearCache() {
        this.ordersCache = null;
        this.cacheTimestamp = null;
        if (typeof window !== 'undefined' && typeof localStorage !== 'undefined') {
            try {
                localStorage.removeItem(this.CACHE_KEY);
                localStorage.removeItem(this.CACHE_TS_KEY);
            }
            catch (error) {
                // Silently fail
            }
        }
    }
    /**
     * List all orders for the authenticated user
     *
     * @param params - Query parameters for filtering, sorting, pagination, and includes
     * @param params.filter - Filter orders (status, gateway, order_number, created_after, created_before)
     * @param params.include - Include related data (product, productPrice, productPrice.product, transactions, fulfillments, subscription)
     * @param params.sort - Sort field (created_at, total, status) - prefix with - for descending
     * @param params.per_page - Items per page (1-100, default: 15)
     * @param params.page - Page number
     *
     * @example
     * // Basic usage
     * const orders = await paycan.orders.list();
     *
     * @example
     * // With filtering and sorting
     * const completedOrders = await paycan.orders.list({
     *   filter: { status: 'completed', created_after: '2024-01-01' },
     *   include: 'productPrice.product',
     *   sort: '-created_at',
     *   per_page: 20
     * });
     */
    async list(params) {
        const result = await this.http.get('/api/user/orders', params);
        this.ordersCache = result.data;
        this.cacheTimestamp = Date.now();
        this.saveCacheToStorage();
        return result;
    }
    /**
     * Get a specific order by ID
     *
     * @param orderId - The order ID
     * @param params - Query parameters
     * @param params.include - Include related data (product, productPrice, productPrice.product, transactions, fulfillments, subscription)
     *
     * @example
     * const order = await paycan.orders.get('order-id-123');
     *
     * @example
     * // With includes
     * const order = await paycan.orders.get('order-id-123', {
     *   include: 'productPrice.product,transactions,fulfillments'
     * });
     */
    async get(orderId, params) {
        return this.http.get(`/api/user/orders/${orderId}`, params);
    }
    /**
     * Get download links for an order
     *
     * @example
     * const downloads = await paycan.orders.getDownloads('order-id-123');
     */
    async getDownloads(orderId) {
        return this.http.get(`/api/user/orders/${orderId}/downloads`);
    }
    /**
     * Get license keys for an order
     *
     * @example
     * const licenses = await paycan.orders.getLicenses('order-id-123');
     */
    async getLicenses(orderId) {
        return this.http.get(`/api/user/orders/${orderId}/licenses`);
    }
}

/**
 * Subscriptions Resource
 *
 * Handle subscription-related operations
 */
class Subscriptions {
    constructor(http, config) {
        this.http = http;
        this.subscriptionsCache = null;
        this.cacheTimestamp = null;
        this.CACHE_KEY = 'paycan_subscriptions_cache';
        this.CACHE_TS_KEY = 'paycan_subscriptions_cache_timestamp';
        // Get cache TTL from config (default 60 seconds), convert to milliseconds
        const cacheTTLSeconds = config.cacheTtl ?? 60;
        this.cacheTtl = cacheTTLSeconds * 1000;
        // Load cache from localStorage on initialization
        this.loadCacheFromStorage();
    }
    /**
     * Load cache from localStorage
     */
    loadCacheFromStorage() {
        if (typeof window === 'undefined' || typeof localStorage === 'undefined') {
            return;
        }
        try {
            const cached = localStorage.getItem(this.CACHE_KEY);
            const timestamp = localStorage.getItem(this.CACHE_TS_KEY);
            if (cached && timestamp) {
                this.subscriptionsCache = JSON.parse(cached);
                this.cacheTimestamp = parseInt(timestamp, 10);
            }
        }
        catch (error) {
            // Silently fail if localStorage is unavailable or data is corrupted
            this.clearCache();
        }
    }
    /**
     * Save cache to localStorage
     */
    saveCacheToStorage() {
        if (typeof window === 'undefined' || typeof localStorage === 'undefined') {
            return;
        }
        try {
            if (this.subscriptionsCache && this.cacheTimestamp) {
                localStorage.setItem(this.CACHE_KEY, JSON.stringify(this.subscriptionsCache));
                localStorage.setItem(this.CACHE_TS_KEY, this.cacheTimestamp.toString());
            }
        }
        catch (error) {
            // Silently fail if localStorage quota exceeded or unavailable
        }
    }
    /**
     * Clear the subscriptions cache
     * Call this after subscription changes (cancel, resume, upgrade)
     */
    clearCache() {
        this.subscriptionsCache = null;
        this.cacheTimestamp = null;
        if (typeof window !== 'undefined' && typeof localStorage !== 'undefined') {
            try {
                localStorage.removeItem(this.CACHE_KEY);
                localStorage.removeItem(this.CACHE_TS_KEY);
            }
            catch (error) {
                // Silently fail
            }
        }
    }
    /**
     * Get cached subscriptions or fetch fresh data
     * PERFORMANCE TIP: This method caches subscriptions to avoid rate limits
     * and improve performance when checking multiple products/prices.
     * Cache can be disabled by setting cacheTtl to 0.
     */
    async getCachedSubscriptions() {
        // If cache is disabled (TTL = 0), always fetch fresh data
        if (this.cacheTtl === 0) {
            const response = await this.list();
            return response.data || [];
        }
        const now = Date.now();
        const isCacheValid = this.subscriptionsCache &&
            this.cacheTimestamp &&
            (now - this.cacheTimestamp) < this.cacheTtl;
        if (isCacheValid) {
            return this.subscriptionsCache;
        }
        // Fetch fresh data
        const response = await this.list();
        this.subscriptionsCache = response.data || [];
        this.cacheTimestamp = now;
        this.saveCacheToStorage();
        return this.subscriptionsCache;
    }
    /**
     * List all subscriptions for the authenticated user
     *
     * @param params - Query parameters for filtering, sorting, pagination, and includes
     * @param params.filter - Filter subscriptions (status, gateway, created_after, created_before)
     * @param params.include - Include related data (product, productPrice, productPrice.product, order, transactions)
     * @param params.sort - Sort field (created_at, status, next_billing_date) - prefix with - for descending
     * @param params.per_page - Items per page (1-100, default: 15)
     * @param params.page - Page number
     *
     * @example
     * // Basic usage
     * const subscriptions = await paycan.subscriptions.list();
     *
     * @example
     * // With filtering and sorting
     * const activeSubscriptions = await paycan.subscriptions.list({
     *   filter: { status: 'active', gateway: 'stripe' },
     *   include: 'productPrice.product',
     *   sort: '-created_at',
     *   per_page: 20
     * });
     */
    async list(params) {
        // Clear cache when explicitly listing to ensure fresh data
        this.clearCache();
        return this.http.get('/api/user/subscriptions', params);
    }
    /**
     * List all active subscriptions (uses cache)
     *
     * @example
     * const activeSubscriptions = await paycan.subscriptions.listActive();
     */
    async listActive() {
        const subscriptions = await this.getCachedSubscriptions();
        return subscriptions.filter((sub) => sub.status === 'active');
    }
    /**
     * Get a specific subscription by ID
     * Checks cache first before making API request
     *
     * @param subscriptionId - The subscription ID
     * @param params - Query parameters
     * @param params.include - Include related data (product, productPrice, productPrice.product, order, transactions)
     *
     * @example
     * const subscription = await paycan.subscriptions.get('sub-123');
     *
     * @example
     * // With includes
     * const subscription = await paycan.subscriptions.get('sub-123', {
     *   include: 'productPrice.product,order,transactions'
     * });
     */
    async get(subscriptionId, params) {
        // Check cache first (only if no specific includes requested)
        if (!params?.include) {
            const cachedSubscriptions = await this.getCachedSubscriptions();
            const cached = cachedSubscriptions.find((sub) => sub.id === Number(subscriptionId));
            if (cached) {
                return { data: cached };
            }
        }
        // Not in cache or specific includes requested, fetch from API
        return this.http.get(`/api/user/subscriptions/${subscriptionId}`, params);
    }
    /**
     * Cancel a subscription
     *
     * @example
     * await paycan.subscriptions.cancel(123);
     */
    async cancel(subscriptionId) {
        const result = await this.http.post(`/api/user/subscriptions/${subscriptionId}/cancel`);
        // Clear cache after mutation
        this.clearCache();
        return result;
    }
    /**
     * Resume a cancelled subscription
     *
     * @example
     * await paycan.subscriptions.resume(123);
     */
    async resume(subscriptionId) {
        const result = await this.http.post(`/api/user/subscriptions/${subscriptionId}/resume`);
        // Clear cache after mutation
        this.clearCache();
        return result;
    }
    /**
     * Change subscription plan
     *
     * @param subscriptionId - The subscription ID
     * @param data - Change subscription data
     * @param data.product_price_id - The new product price ID
     * @param data.prorate - Whether to prorate the change (optional)
     *
     * @example
     * await paycan.subscriptions.change(123, { product_price_id: 'price-456', prorate: true });
     */
    async change(subscriptionId, data) {
        const result = await this.http.post(`/api/user/subscriptions/${subscriptionId}/change`, data);
        // Clear cache after mutation
        this.clearCache();
        return result;
    }
}

/**
 * Checkout Resource
 *
 * Handle checkout and payment portal operations
 */
class Checkout {
    constructor(http, orders, subscriptions) {
        this.http = http;
        this.orders = orders;
        this.subscriptions = subscriptions;
    }
    /**
     * Create a checkout session
     *
     * @example
     * const session = await paycan.checkout.create({
     *   product_id: 1,
     *   product_price_id: 1,
     *   gateway: 'stripe',
     *   billing_email: 'user@example.com', // Optional for authenticated users
     *   billing_name: 'John Doe',          // Optional for authenticated users
     *   quantity: 1                         // Optional, defaults to 1
     * });
     * window.location.href = session.checkout_url;
     */
    async create(data) {
        // Skip auth check if billing_email is provided (guest checkout)
        const skipAuthCheck = !!data.billing_email;
        const response = await this.http.post('/api/user/checkout', data, skipAuthCheck);
        // Clear both orders and subscriptions cache after checkout
        // to ensure fresh data when user returns from payment
        this.orders.clearCache();
        this.subscriptions.clearCache();
        return response.checkout;
    }
    /**
     * Get customer portal URL for managing subscriptions
     *
     * @example
     * const portal = await paycan.checkout.getPortalUrl();
     * window.location.href = portal.url;
     */
    async getPortalUrl(returnUrl) {
        const response = await this.http.post('/api/user/checkout/portal', returnUrl ? { return_url: returnUrl } : undefined);
        return response.portal;
    }
    /**
     * Preview checkout totals
     *
     * Get a preview of the order totals. Tax is calculated and collected by the
     * payment gateway at checkout (e.g. Stripe Tax), so previews are pre-tax
     * before creating the actual checkout session.
     *
     * @example
     * const preview = await paycan.checkout.preview({
     *   product_price_id: 1,
     *   gateway: 'stripe',
     *   quantity: 2,
     *   billing_country: 'US',
     *   billing_state: 'CA'
     * });
     * console.log('Total:', preview.selected_price.final_price);
     */
    async preview(params) {
        const queryParams = new URLSearchParams();
        if (params.product_id) {
            queryParams.append('product_id', params.product_id.toString());
        }
        if (params.product_price_id) {
            queryParams.append('product_price_id', params.product_price_id.toString());
        }
        if (params.selected_price_id) {
            queryParams.append('selected_price_id', params.selected_price_id.toString());
        }
        if (params.gateway) {
            queryParams.append('gateway', params.gateway);
        }
        if (params.quantity) {
            queryParams.append('quantity', params.quantity.toString());
        }
        if (params.billing_country) {
            queryParams.append('billing_country', params.billing_country);
        }
        if (params.billing_state) {
            queryParams.append('billing_state', params.billing_state);
        }
        // Preview is now public endpoint, skip auth check
        return this.http.get(`/api/user/checkout/preview?${queryParams.toString()}`, undefined, true);
    }
}

/**
 * Products Resource
 *
 * Handle product-related operations
 */
class Products {
    constructor(http) {
        this.http = http;
    }
    /**
     * List all active products
     *
     * @param params - Query parameters for filtering, sorting, pagination, and includes
     * @param params.filter - Filter products (type)
     * @param params.include - Include related data (prices)
     * @param params.sort - Sort field (created_at, title) - prefix with - for descending
     * @param params.per_page - Items per page (1-100, default: 15)
     * @param params.page - Page number
     *
     * @example
     * // Basic usage
     * const products = await paycan.products.list();
     *
     * @example
     * // With filtering and sorting
     * const digitalProducts = await paycan.products.list({
     *   filter: { type: 'digital' },
     *   include: 'activePrices',
     *   sort: 'title',
     *   per_page: 20
     * });
     */
    async list(params) {
        return this.http.get('/api/user/products', params, true);
    }
    /**
     * Get a specific product by ID
     *
     * @param productId - The product ID
     * @param params - Query parameters
     * @param params.include - Include related data (prices)
     *
     * @example
     * const product = await paycan.products.get('product-123');
     *
     * @example
     * // With includes
     * const product = await paycan.products.get('product-123', {
     *   include: 'activePrices'
     * });
     */
    async get(productId, params) {
        return this.http.get(`/api/user/products/${productId}`, params, true);
    }
}

/**
 * Transactions Resource
 *
 * Handle transaction-related operations
 */
class Transactions {
    constructor(http) {
        this.http = http;
    }
    /**
     * List all transactions for the authenticated user
     *
     * @param params - Query parameters for filtering, sorting, pagination, and includes
     * @param params.filter - Filter transactions (type, status, gateway, created_after, created_before)
     * @param params.include - Include related data (order, subscription)
     * @param params.sort - Sort field (created_at, amount) - prefix with - for descending
     * @param params.per_page - Items per page (1-100, default: 15)
     * @param params.page - Page number
     *
     * @example
     * // Basic usage
     * const transactions = await paycan.transactions.list();
     *
     * @example
     * // With filtering and sorting
     * const completedTransactions = await paycan.transactions.list({
     *   filter: { status: 'completed', type: 'charge' },
     *   sort: '-created_at',
     *   per_page: 20
     * });
     */
    async list(params) {
        return this.http.get('/api/user/transactions', params);
    }
    /**
     * Get a specific transaction by ID
     *
     * @param transactionId - The transaction ID
     * @param params - Query parameters
     * @param params.include - Include related data (order, subscription)
     *
     * @example
     * const transaction = await paycan.transactions.get('txn-123');
     *
     * @example
     * // With includes
     * const transaction = await paycan.transactions.get('txn-123', {
     *   include: 'order'
     * });
     */
    async get(transactionId, params) {
        return this.http.get(`/api/user/transactions/${transactionId}`, params);
    }
}

/**
 * Shared Styles for PayCan Modals
 *
 * This module contains reusable CSS styles for all PayCan modals
 * to ensure consistency and reduce code duplication.
 */
/**
 * Get base modal styles (overlay, container, header, footer, etc.)
 */
function getBaseModalStyles() {
    return `
    /* PayCan Modal Base Styles */

    /* CSS Variables for Customization */
    .paycan-modal-overlay {
      --paycan-accent: #3b82f6;
      --paycan-accent-hover: #2563eb;
      --paycan-accent-light: #eff6ff;
      --paycan-accent-dark: #1e3a8a;
    }

    /* Overlay */
    .paycan-modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 999999;
      padding: 1rem;
      opacity: 0;
      transition: opacity 0.2s ease-in-out;
    }

    .paycan-modal-overlay.paycan-show {
      opacity: 1;
    }

    /* Modal Container */
    .paycan-modal {
      width: 100%;
      max-width: 600px;
      max-height: 90vh;
      border-radius: 12px;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
      transform: scale(0.95);
      transition: transform 0.2s ease-in-out;
    }

    .paycan-modal.paycan-show {
      transform: scale(1);
    }

    .paycan-modal-wide {
      max-width: 800px;
    }

    /* Theme Colors */
    .paycan-modal.paycan-theme-light {
      background: #f9fafb;
      color: #111827;
    }

    .paycan-modal.paycan-theme-dark {
      background: #111827;
      color: #f9fafb;
    }

    /* Modal Header */
    .paycan-modal-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 1rem 1.25rem;
      border-bottom: 1px solid;
      flex-shrink: 0;
    }

    .paycan-theme-light .paycan-modal-header {
      border-bottom-color: #e5e7eb;
    }

    .paycan-theme-dark .paycan-modal-header {
      border-bottom-color: #374151;
    }

    .paycan-modal-header-content {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .paycan-modal-title {
      font-size: 1.25rem;
      font-weight: 500;
      margin: 0;
    }

    .paycan-header-actions {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    /* Close Button */
    .paycan-close-btn {
      background: none;
      border: none;
      font-size: 1.5rem;
      font-weight: 200;
      cursor: pointer;
      padding: 0;
      line-height: 1;
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 4px;
      transition: background 0.15s;
      box-shadow: none;
    }

    .paycan-theme-light .paycan-close-btn {
      color: #6b7280;
    }

    .paycan-theme-dark .paycan-close-btn {
      color: #9ca3af;
    }

    .paycan-theme-light .paycan-close-btn:hover {
      background: #f3f4f6;
    }

    .paycan-theme-dark .paycan-close-btn:hover {
      background: #374151;
    }

    /* Modal Body */
    .paycan-modal-body {
      flex: 1;
      overflow-y: auto;
      padding: 1.5rem;
    }

    /* Modal Footer */
    .paycan-modal-footer {
      padding: 1rem 1.25rem;
      border-top: 1px solid;
      flex-shrink: 0;
    }

    .paycan-theme-light .paycan-modal-footer {
      border-top-color: #e5e7eb;
    }

    .paycan-theme-dark .paycan-modal-footer {
      border-top-color: #374151;
    }
  `;
}
/**
 * Get toast notification styles
 */
function getToastStyles() {
    return `
    /* Toast Notification */
    .paycan-toast {
      position: absolute;
      top: 5rem;
      left: 50%;
      transform: translateX(-50%) translateY(-100px);
      padding: 0.75rem 1rem;
      border-radius: 8px;
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
      opacity: 0;
      transition: all 0.3s;
      pointer-events: none;
      z-index: 1000;
      max-width: 90%;
    }

    .paycan-toast.show {
      opacity: 1;
      transform: translateX(-50%) translateY(0);
    }

    .paycan-toast-content {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.875rem;
    }

    .paycan-toast-success {
      background: #10b981;
      color: white;
    }

    .paycan-toast-error {
      background: #ef4444;
      color: white;
    }

    .paycan-toast-info {
      background: #3b82f6;
      color: white;
    }
  `;
}
/**
 * Get common button styles
 */
function getButtonStyles() {
    return `
    /* Buttons */
    .paycan-btn {
      padding: 0.5rem 1rem;
      border-radius: 6px;
      font-size: 0.875rem;
      font-weight: 400;
      cursor: pointer;
      border: 1px solid transparent;
      transition: all 0.15s;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
    }

    .paycan-btn:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }

    .paycan-btn-sm {
      padding: 0.375rem 0.75rem;
      font-size: 0.8125rem;
    }

    .paycan-btn-lg {
      padding: 0.75rem 1.5rem;
      font-size: 1rem;
      font-weight: 400;
    }

    .paycan-btn-primary {
      background: var(--paycan-accent);
      color: white;
    }

    .paycan-btn-primary:hover:not(:disabled) {
      background: var(--paycan-accent-hover);
    }

    .paycan-btn-primary:active:not(:disabled) {
      transform: scale(0.98);
    }

    .paycan-btn-secondary {
      background: transparent;
      border: 1px solid;
    }

    .paycan-theme-light .paycan-btn-secondary {
      border-color: #d1d5db;
      color: #111827;
    }

    .paycan-theme-dark .paycan-btn-secondary {
      border-color: #4b5563;
      color: #f9fafb;
    }

    .paycan-theme-light .paycan-btn-secondary {
      background: #ffffff;
      color: #374151;
    }

    .paycan-theme-dark .paycan-btn-secondary {
      background: #374151;
      color: #f9fafb;
      border-color: #4b5563;
    }

    .paycan-theme-light .paycan-btn-secondary:hover:not(:disabled) {
      background: #f9fafb;
    }

    .paycan-theme-dark .paycan-btn-secondary:hover:not(:disabled) {
      background: #4b5563;
    }

    .paycan-btn-danger {
      background: #ef4444;
      color: white;
    }

    .paycan-btn-danger:hover:not(:disabled) {
      background: #dc2626;
    }

    .paycan-btn-success {
      background: #10b981;
      color: white;
    }

    .paycan-btn-success:hover:not(:disabled) {
      background: #059669;
    }

    .paycan-btn-info {
      background: #3b82f6;
      color: white;
    }

    .paycan-btn-info:hover:not(:disabled) {
      background: #2563eb;
    }

    .paycan-btn-warning {
      background: #f59e0b;
      color: white;
    }

    .paycan-btn-warning:hover:not(:disabled) {
      background: #d97706;
    }

    .paycan-btn-purple {
      background: #8b5cf6;
      color: white;
    }

    .paycan-btn-purple:hover:not(:disabled) {
      background: #7c3aed;
    }

    /* Button Group */
    .paycan-button-group {
      display: flex;
      gap: 0.75rem;
      justify-content: flex-end;
    }

    /* Push Button Animation */
    .paycan-btn-push {
      position: relative;
      transition: all 0.2s ease;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .paycan-btn-push:hover:not(:disabled) {
      transform: translateY(-2px);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .paycan-btn-push:active:not(:disabled) {
      transform: translateY(0);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
  `;
}
/**
 * Get common card styles
 */
function getCardStyles() {
    return `
    /* Card */
    .paycan-card {
      border-radius: 8px;
      border: 1px solid;
      overflow: hidden;
    }

    .paycan-theme-light .paycan-card {
      background: #ffffff;
      border-color: #e5e7eb;
    }

    .paycan-theme-dark .paycan-card {
      background: #1f2937;
      border-color: #4b5563;
    }

    .paycan-card-header {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      padding: 1rem;
      gap: 1rem;
      border-bottom: 1px solid;
    }

    .paycan-theme-light .paycan-card-header {
      border-bottom-color: #e5e7eb;
    }

    .paycan-theme-dark .paycan-card-header {
      border-bottom-color: #374151;
    }

    .paycan-card-title {
      font-size: 1rem;
      font-weight: 500;
      margin: 0 0 0.25rem 0;
    }

    .paycan-card-subtitle {
      font-size: 0.875rem;
      margin: 0;
    }

    .paycan-theme-light .paycan-card-subtitle {
      color: #6b7280;
    }

    .paycan-theme-dark .paycan-card-subtitle {
      color: #9ca3af;
    }

    .paycan-card-body {
      padding: 1rem;
    }

    .paycan-card-footer {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 0.5rem;
      padding: 1rem;
      border-top: 1px solid;
    }

    .paycan-theme-light .paycan-card-footer {
      border-top-color: #e5e7eb;
      background: #f9fafb;
    }

    .paycan-theme-dark .paycan-card-footer {
      border-top-color: #374151;
      background: #111827;
    }
  `;
}
/**
 * Get common badge styles
 */
function getBadgeStyles() {
    return `
    /* Badge */
    .paycan-badge {
      font-size: 0.75rem;
      padding: 0.25rem 0.75rem;
      border-radius: 9999px;
      font-weight: 500;
      flex-shrink: 0;
    }

    .paycan-badge-success {
      background: #d1fae5;
      color: #065f46;
    }

    .paycan-theme-dark .paycan-badge-success {
      background: #064e3b;
      color: #6ee7b7;
    }

    .paycan-badge-warning {
      background: #fef3c7;
      color: #92400e;
    }

    .paycan-theme-dark .paycan-badge-warning {
      background: #78350f;
      color: #fcd34d;
    }

    .paycan-badge-error {
      background: #fee2e2;
      color: #991b1b;
    }

    .paycan-theme-dark .paycan-badge-error {
      background: #7f1d1d;
      color: #fca5a5;
    }

    .paycan-badge-info {
      background: #dbeafe;
      color: #1e40af;
    }

    .paycan-theme-dark .paycan-badge-info {
      background: #1e3a8a;
      color: #93c5fd;
    }
  `;
}
/**
 * Get loading state styles
 */
function getLoadingStyles() {
    return `
    /* Loading State */
    .paycan-loading-state {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 3rem 1rem;
    }

    .paycan-spinner {
      width: 40px;
      height: 40px;
      border: 3px solid;
      border-radius: 50%;
      border-top-color: var(--paycan-accent);
      animation: paycan-spin 0.6s linear infinite;
    }

    .paycan-theme-light .paycan-spinner {
      border-color: #e5e7eb;
    }

    .paycan-theme-dark .paycan-spinner {
      border-color: #374151;
    }

    @keyframes paycan-spin {
      to { transform: rotate(360deg); }
    }

    .paycan-loading-state p {
      margin-top: 1rem;
      font-size: 0.875rem;
    }

    .paycan-theme-light .paycan-loading-state p {
      color: #6b7280;
    }

    .paycan-theme-dark .paycan-loading-state p {
      color: #9ca3af;
    }
  `;
}
/**
 * Get empty state styles
 */
function getEmptyStateStyles() {
    return `
    /* Empty State */
    .paycan-empty-state {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 3rem 1rem;
      text-align: center;
    }

    .paycan-empty-state svg {
      margin-bottom: 1rem;
    }

    .paycan-theme-light .paycan-empty-state svg {
      color: #9ca3af;
    }

    .paycan-theme-dark .paycan-empty-state svg {
      color: #6b7280;
    }

    .paycan-empty-state p {
      font-size: 0.875rem;
      margin: 0;
    }

    .paycan-theme-light .paycan-empty-state p {
      color: #6b7280;
    }

    .paycan-theme-dark .paycan-empty-state p {
      color: #9ca3af;
    }
  `;
}
/**
 * Get all shared styles combined
 */
function getAllSharedStyles() {
    return [
        getBaseModalStyles(),
        getToastStyles(),
        getButtonStyles(),
        getCardStyles(),
        getBadgeStyles(),
        getLoadingStyles(),
        getEmptyStateStyles(),
    ].join('\n');
}
/**
 * Base toast helper for consistent behavior across all modals
 */
class ToastHelper {
    /**
     * Show toast notification
     */
    static showToast(modal, message, type = 'info') {
        const toast = modal?.querySelector('.paycan-toast');
        const content = modal?.querySelector('.paycan-toast-content');
        if (!toast || !content)
            return;
        const icon = type === 'success' ? '✓' : type === 'error' ? '✕' : 'ℹ';
        content.innerHTML = `<span>${icon}</span><span>${ToastHelper.escapeHtml(message)}</span>`;
        toast.classList.add('show', `paycan-toast-${type}`);
        setTimeout(() => {
            toast.classList.remove('show', `paycan-toast-${type}`);
        }, 10000); // 10s
    }
    /**
     * Escape HTML to prevent XSS
     */
    static escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

/**
 * Shared utility functions for formatting data across PayCan SDK components
 * This module eliminates code duplication and ensures consistent formatting
 */
class Formatters {
    /**
     * Format price with currency
     */
    static formatPrice(amount, currency) {
        if (!currency) {
            return amount.toFixed(2);
        }
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: currency.toUpperCase(),
        }).format(amount);
    }
    /**
     * Format date with optional time
     */
    static formatDate(date, includeTime = false) {
        const options = {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            ...(includeTime && { hour: '2-digit', minute: '2-digit' })
        };
        return new Date(date).toLocaleDateString('en-US', options);
    }
    /**
     * Format billing period text
     */
    static getBillingPeriodText(period) {
        if (period === 'once')
            return 'one-time';
        // Handle different period formats
        const periodMap = {
            'day': 'day',
            'week': 'week',
            'month': 'month',
            'year': 'year'
        };
        return periodMap[period] || period;
    }
    /**
     * Format status text (capitalize and replace underscores)
     */
    static formatStatus(status) {
        return status.charAt(0).toUpperCase() + status.slice(1).replace(/_/g, ' ');
    }
    /**
     * Format payment gateway name
     */
    static formatGateway(gateway) {
        const gatewayMap = {
            'stripe': 'Stripe',
            'paypal': 'PayPal',
            'razorpay': 'Razorpay',
            'paddle': 'Paddle'
        };
        return gatewayMap[gateway] || gateway.charAt(0).toUpperCase() + gateway.slice(1);
    }
    /**
     * Escape HTML to prevent XSS attacks
     */
    static escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    /**
     * Trim text to specified length with ellipsis
     */
    static trimText(text, maxLength) {
        if (text.length <= maxLength)
            return text;
        return text.substring(0, maxLength).trim() + '...';
    }
    /**
     * Trim text to specified word count
     */
    static trimWords(text, maxWords) {
        const words = text.split(' ');
        if (words.length <= maxWords)
            return text;
        return words.slice(0, maxWords).join(' ') + '...';
    }
    /**
     * Validate email format
     */
    static isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
}

/**
 * Shared event handler utilities for PayCan SDK components
 * This module provides reusable event handling logic
 */
class EventHandlers {
    /**
     * Create an escape key handler for modal closing
     */
    static createEscapeHandler(closeCallback) {
        return (e) => {
            if (e.key === 'Escape') {
                closeCallback();
            }
        };
    }
    /**
     * Create a pagination handler
     */
    static createPaginationHandler(loadCallback) {
        return async (e) => {
            const target = e.target;
            const page = target.getAttribute('data-page');
            if (page) {
                await loadCallback(parseInt(page, 10));
            }
        };
    }
    /**
     * Create a debounced function
     */
    static debounce(func, wait) {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => func(...args), wait);
        };
    }
    /**
     * Create a throttled function
     */
    static throttle(func, limit) {
        let inThrottle;
        return (...args) => {
            if (!inThrottle) {
                func(...args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }
}

/**
 * PayCan Checkout Modal Web Component
 *
 * A framework-agnostic checkout modal that can be embedded anywhere
 */
class CheckoutModal {
    constructor(sdk, options) {
        this.container = null;
        this.shadowRoot = null;
        this.overlay = null;
        this.modal = null;
        this.previewData = null;
        this.selectedPriceId = null;
        this.selectedGateway = 'stripe';
        this.loading = false;
        /**
         * Handle escape key press
         */
        this.handleEscapeKey = EventHandlers.createEscapeHandler(() => this.close());
        this.sdk = sdk;
        this.options = options;
        this.selectedPriceId = options.priceId || null;
    }
    /**
     * Open the modal and load preview data
     */
    async open() {
        try {
            let sessionExpired = false;
            if (this.sdk.isAuthenticated && this.sdk.isAuthenticated()) {
                try {
                    await this.sdk.me();
                }
                catch (err) {
                    if (err?.status === 401 && this.sdk.logout) {
                        this.sdk.logout();
                        sessionExpired = true;
                    }
                }
            }
            // Load preview data
            await this.loadPreview();
            // Create and show modal
            this.createModal();
            this.show();
            // Inform user after modal is visible
            if (sessionExpired) {
                this.showToast('Session expired. Continue as guest or log in.', 'info');
            }
        }
        catch (error) {
            this.handleError(error);
        }
    }
    /**
     * Close and destroy the modal
     */
    close() {
        // Remove the entire shadow DOM container
        if (this.container) {
            this.container.remove();
            this.container = null;
            this.shadowRoot = null;
            this.overlay = null;
            this.modal = null;
        }
        // Remove escape key listener
        document.removeEventListener('keydown', this.handleEscapeKey);
        if (this.options.onCancel) {
            this.options.onCancel();
        }
    }
    /**
     * Load preview data from API
     */
    async loadPreview() {
        const params = {
            quantity: 1,
        };
        if (this.options.productId) {
            params.product_id = this.options.productId;
            if (this.selectedPriceId) {
                params.selected_price_id = this.selectedPriceId;
            }
        }
        else if (this.selectedPriceId) {
            params.product_price_id = this.selectedPriceId;
        }
        this.previewData = await this.sdk.checkout.preview(params);
        // Set default selected price if not set
        if (!this.selectedPriceId && this.previewData.selected_price) {
            this.selectedPriceId = this.previewData.selected_price.id;
        }
        // Set default gateway from available payment methods
        if (this.previewData.payment_methods.length > 0) {
            this.selectedGateway = this.previewData.payment_methods[0].key;
        }
    }
    /**
     * Create modal DOM structure with Shadow DOM
     */
    createModal() {
        const isDark = this.isDarkMode();
        const themeClass = isDark ? 'paycan-theme-dark' : 'paycan-theme-light';
        // Create container element for Shadow DOM
        this.container = document.createElement('div');
        this.container.setAttribute('id', 'paycan-checkout-modal');
        // Attach Shadow DOM
        this.shadowRoot = this.container.attachShadow({ mode: 'open' });
        // Create style element inside shadow root
        const styleEl = document.createElement('style');
        styleEl.textContent = this.getStyles();
        this.shadowRoot.appendChild(styleEl);
        // Create overlay inside shadow root
        this.overlay = document.createElement('div');
        this.overlay.className = `paycan-modal-overlay ${themeClass}`;
        // Create modal container
        this.modal = document.createElement('div');
        this.modal.className = `paycan-modal ${themeClass}`;
        this.modal.innerHTML = this.getModalContent();
        this.overlay.appendChild(this.modal);
        this.shadowRoot.appendChild(this.overlay);
        // Append container to body
        document.body.appendChild(this.container);
        // Add event listeners
        this.attachEventListeners();
        // Close on overlay click
        this.overlay.addEventListener('click', (e) => {
            if (e.target === this.overlay) {
                this.close();
            }
        });
        // Close on Escape key
        document.addEventListener('keydown', this.handleEscapeKey);
    }
    /**
     * Get all modal CSS styles
     */
    getStyles() {
        return `
      /* PayCan Checkout Modal Styles */

      ${getAllSharedStyles()}

      /* Checkout Modal Specific Styles */

      /* Adjust modal max-width for checkout */
      .paycan-modal {
        max-width: 500px;
      }

      /* Product Card */
      .paycan-product-card {
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid;
      }

      .paycan-theme-light .paycan-product-card {
        background: #ffffff;
        border-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-product-card {
        background: #1f2937;
        border-color: #4b5563;
      }

      .paycan-product-title {
        font-size: 1.125rem;
        font-weight: 500;
        margin: 0 0 0.5rem 0;
      }

      .paycan-product-description {
        font-size: 0.875rem;
        margin: 0;
      }

      .paycan-theme-light .paycan-product-description {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-product-description {
        color: #d1d5db;
      }

      /* Form Fields */
      .paycan-form-group {
        margin-bottom: 1.5rem;
      }

      .paycan-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
      }

      .paycan-label-required {
        color: #ef4444;
      }

      .paycan-input {
        width: 100%;
        padding: 0.625rem;
        border: 1px solid;
        border-radius: 6px;
        font-size: 0.875rem;
        box-sizing: border-box;
      }

      .paycan-theme-light .paycan-input {
        border-color: #d1d5db;
        background: #ffffff;
        color: #111827;
      }

      .paycan-theme-dark .paycan-input {
        border-color: #4b5563;
        background: #374151;
        color: #f9fafb;
      }

      .paycan-help-text {
        font-size: 0.75rem;
        margin: 0.375rem 0 0 0;
      }

      .paycan-theme-light .paycan-help-text {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-help-text {
        color: #9ca3af;
      }

      /* Price Selection */
      .paycan-price-options {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
      }

      .paycan-price-option {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        border: 2px solid;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.15s;
      }

      .paycan-theme-light .paycan-price-option {
        border-color: #e5e7eb;
        background: #ffffff;
      }

      .paycan-theme-dark .paycan-price-option {
        border-color: #4b5563;
        background: #374151;
      }

      .paycan-price-option.selected {
        border-color: var(--paycan-accent);
      }

      .paycan-theme-light .paycan-price-option.selected {
        background: var(--paycan-accent-light);
      }

      .paycan-theme-dark .paycan-price-option.selected {
        background: var(--paycan-accent-dark);
      }

      .paycan-price-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
      }

      .paycan-price-option-icon {
        margin-right: 0.75rem;
        flex-shrink: 0;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
      }

      .paycan-theme-light .paycan-price-option-icon {
        background: #e5e7eb;
      }

      .paycan-theme-dark .paycan-price-option-icon {
        background: #4b5563;
      }

      .paycan-price-option.selected .paycan-price-option-icon {
        background: var(--paycan-accent);
      }

      .paycan-price-option-icon svg {
        width: 10px;
        height: 10px;
        opacity: 0;
        transition: opacity 0.15s;
      }

      .paycan-price-option.selected .paycan-price-option-icon svg {
        opacity: 1;
      }

      .paycan-price-details {
        flex: 1;
      }

      .paycan-price-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
      }

      .paycan-price-name {
        font-weight: 400;
        flex: 1;
        font-size: 0.875rem;
      }

      .paycan-price-amount-group {
        text-align: right;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
      }

      .paycan-price-amount {
        font-weight: 400;
        font-size: 1rem;
      }

      .paycan-price-period {
        font-size: 0.75rem;
        white-space: nowrap;
      }

      .paycan-theme-light .paycan-price-period {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-price-period {
        color: #9ca3af;
      }

      /* Dropdown */
      .paycan-select {
        width: 100%;
        padding: 0.625rem;
        border: 1px solid;
        border-radius: 6px;
        font-size: 0.875rem;
        cursor: pointer;
      }

      .paycan-theme-light .paycan-select {
        border-color: #d1d5db;
        background: #ffffff;
        color: #111827;
      }

      .paycan-theme-dark .paycan-select {
        border-color: #4b5563;
        background: #374151;
        color: #f9fafb;
      }

      /* Price Breakdown */
      .paycan-price-breakdown {
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid;
      }

      .paycan-theme-light .paycan-price-breakdown {
        background: #ffffff;
        border-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-price-breakdown {
        background: #1f2937;
        border-color: #4b5563;
      }

      .paycan-breakdown-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
      }

      .paycan-breakdown-label {
        font-size: 0.875rem;
      }

      .paycan-theme-light .paycan-breakdown-label {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-breakdown-label {
        color: #9ca3af;
      }

      .paycan-breakdown-value {
        font-size: 0.875rem;
        font-weight: 500;
      }

      .paycan-breakdown-divider {
        margin: 0.75rem 0;
        padding-top: 0.75rem;
        border-top: 1px solid;
      }

      .paycan-theme-light .paycan-breakdown-divider {
        border-top-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-breakdown-divider {
        border-top-color: #374151;
      }

      .paycan-breakdown-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
      }

      .paycan-breakdown-total-label {
        font-weight: 400;
      }

      .paycan-breakdown-total-value {
        text-align: right;
      }

      .paycan-breakdown-total-amount {
        font-size: 1.5rem;
        font-weight: 400;
      }

      .paycan-breakdown-total-period {
        font-size: 0.75rem;
      }

      .paycan-theme-light .paycan-breakdown-total-period {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-breakdown-total-period {
        color: #9ca3af;
      }

      /* Payment Methods */
      .paycan-payment-methods {
        display: grid;
        gap: 0.75rem;
      }

      .paycan-payment-methods.grid-1 {
        grid-template-columns: 1fr;
      }

      .paycan-payment-methods.grid-2 {
        grid-template-columns: repeat(2, 1fr);
      }

      .paycan-payment-method {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        border: 2px solid;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.15s;
      }

      .paycan-theme-light .paycan-payment-method {
        border-color: #e5e7eb;
        background: #ffffff;
      }

      .paycan-theme-dark .paycan-payment-method {
        border-color: #4b5563;
        background: #374151;
      }

      .paycan-payment-method.selected {
        border-color: var(--paycan-accent);
      }

      .paycan-theme-light .paycan-payment-method.selected {
        background: var(--paycan-accent-light);
      }

      .paycan-theme-dark .paycan-payment-method.selected {
        background: var(--paycan-accent-dark);
      }

      .paycan-payment-method.full-width {
        grid-column: 1 / -1;
      }

      .paycan-payment-method input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
      }

      .paycan-payment-method-icon {
        margin-right: 0.75rem;
        flex-shrink: 0;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
      }

      .paycan-theme-light .paycan-payment-method-icon {
        background: #e5e7eb;
      }

      .paycan-theme-dark .paycan-payment-method-icon {
        background: #4b5563;
      }

      .paycan-payment-method.selected .paycan-payment-method-icon {
        background: var(--paycan-accent);
      }

      .paycan-payment-method-icon svg {
        width: 10px;
        height: 10px;
        opacity: 0;
        transition: opacity 0.15s;
      }

      .paycan-payment-method.selected .paycan-payment-method-icon svg {
        opacity: 1;
      }

      .paycan-payment-method-info {
        flex: 1;
        min-width: 0;
      }

      .paycan-payment-method-name {
        font-weight: 400;
        margin-bottom: 0.25rem;
        font-size: 0.875rem;
      }

      .paycan-payment-method-description {
        font-size: 0.75rem;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      .paycan-theme-light .paycan-payment-method-description {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-payment-method-description {
        color: #9ca3af;
      }

      /* Responsive */
      @media (max-width: 640px) {
        .paycan-modal {
          max-width: 100%;
        }

        .paycan-payment-methods.grid-2 {
          grid-template-columns: 1fr;
        }

        .paycan-button-group {
          flex-direction: column-reverse;
        }

        .paycan-btn {
          width: 100%;
        }
      }
    `;
    }
    /**
     * Detect if dark mode should be used
     */
    isDarkMode() {
        const theme = this.options.theme || 'auto';
        if (theme === 'dark') {
            return true;
        }
        else if (theme === 'light') {
            return false;
        }
        else {
            // Auto mode - detect from system preference
            return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        }
    }
    /**
     * Get modal HTML content
     */
    getModalContent() {
        if (!this.previewData) {
            return '<div class="paycan-modal-body" style="padding: 2rem; text-align: center;">Loading...</div>';
        }
        const isAuthenticated = this.sdk.isAuthenticated();
        const product = this.previewData.product;
        const selectedPrice = this.previewData.selected_price;
        const prices = this.previewData.prices;
        const paymentMethods = this.previewData.payment_methods;
        return `
      <!-- Fixed Header -->
      <div class="paycan-modal-header">
        <h2 class="paycan-modal-title">🛒 Checkout</h2>
        <button class="paycan-close-btn" aria-label="Close">×</button>
      </div>

      <!-- Toast Notification -->
      <div class="paycan-toast">
        <div class="paycan-toast-content"></div>
      </div>

      <!-- Scrollable Body -->
      <div class="paycan-modal-body">
        <!-- Product Info -->
        <div class="paycan-product-card">
          <h3 class="paycan-product-title">${Formatters.escapeHtml(product.name)}</h3>
          ${product.description ? `<p class="paycan-product-description">${Formatters.escapeHtml(Formatters.trimWords(product.description, 15))}</p>` : ''}
        </div>

        ${prices.length > 1 ? this.getPriceSelectionField(prices) : ''}

        ${selectedPrice ? this.getPriceBreakdown(selectedPrice) : ''}

        ${!isAuthenticated ? this.getGuestEmailField() : ''}

        ${paymentMethods.length > 0 ? this.getPaymentMethodsField(paymentMethods) : ''}

        <!-- Security Notice -->
        <div class="paycan-product-card">
          <p class="paycan-help-text" style="margin: 0;">
            🔒 Your payment information is secure and encrypted. We never store your card details.
          </p>
        </div>
      </div>

      <!-- Fixed Footer -->
      <div class="paycan-modal-footer">
        <div class="paycan-button-group">
          <button class="paycan-btn paycan-btn-secondary paycan-cancel-btn">
            Cancel
          </button>
          <button class="paycan-btn paycan-btn-primary paycan-btn-push paycan-checkout-btn" ${this.loading ? 'disabled' : ''}>
            ${this.loading ? 'Processing...' : selectedPrice ? `Pay ${Formatters.formatPrice(selectedPrice.final_price, selectedPrice.currency)}` : 'Continue'}
          </button>
        </div>
      </div>

      <style>
        @keyframes slideDown {
          from {
            opacity: 0;
            transform: translateY(-10px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }
      </style>
    `;
    }
    /**
     * Get guest email field HTML
     */
    getGuestEmailField() {
        return `
      <div class="paycan-form-group">
        <label class="paycan-label">
          Email Address <span class="paycan-label-required">*</span>
        </label>
        <input
          type="email"
          class="paycan-input paycan-email-input"
          placeholder="your@email.com"
          required
        />
      </div>
    `;
    }
    /**
     * Get price selection field HTML
     */
    getPriceSelectionField(prices) {
        if (prices.length < 3) {
            // Radio buttons
            return `
        <div class="paycan-form-group">
          <label class="paycan-label">Select Plan</label>
          <div class="paycan-price-options">
            ${prices.map(price => `
              <label class="paycan-price-option ${this.selectedPriceId === price.id ? 'selected' : ''}">
                <input
                  type="radio"
                  name="price"
                  value="${price.id}"
                  class="paycan-price-radio"
                  ${this.selectedPriceId === price.id ? 'checked' : ''}
                />
                <div class="paycan-price-option-icon">
                  <svg viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 3L4.5 8.5L2 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </div>
                <div class="paycan-price-details">
                  <div class="paycan-price-header">
                    <span class="paycan-price-name">${Formatters.escapeHtml(price.name)}</span>
                    <div class="paycan-price-amount-group">
                      <span class="paycan-price-amount">${Formatters.formatPrice(price.final_price, price.currency)}</span>
                      <span class="paycan-price-period">${Formatters.getBillingPeriodText(price.billing_period)}${price.trial_days ? ` • ${price.trial_days} days trial` : ''}</span>
                    </div>
                  </div>
                </div>
              </label>
            `).join('')}
          </div>
        </div>
      `;
        }
        else {
            // Dropdown
            return `
        <div class="paycan-form-group">
          <label class="paycan-label">Select Plan</label>
          <select class="paycan-select paycan-price-select">
            ${prices.map(price => `
              <option value="${price.id}" ${this.selectedPriceId === price.id ? 'selected' : ''}>
                ${Formatters.escapeHtml(price.name)} - ${Formatters.formatPrice(price.final_price, price.currency)} ${Formatters.getBillingPeriodText(price.billing_period)}
              </option>
            `).join('')}
          </select>
        </div>
      `;
        }
    }
    /**
     * Get price breakdown HTML
     */
    getPriceBreakdown(price) {
        return `
      <div class="paycan-price-breakdown">
        <div class="paycan-breakdown-row">
          <span class="paycan-breakdown-label">Subtotal</span>
          <span class="paycan-breakdown-value">${Formatters.formatPrice(price.subtotal, price.currency)}</span>
        </div>
        <div class="paycan-breakdown-divider"></div>
        <div class="paycan-breakdown-total">
          <span class="paycan-breakdown-total-label">Total</span>
          <div class="paycan-breakdown-total-value">
            <div class="paycan-breakdown-total-amount">${Formatters.formatPrice(price.final_price, price.currency)}</div>
            <div class="paycan-breakdown-total-period">${Formatters.getBillingPeriodText(price.billing_period)}</div>
          </div>
        </div>
      </div>
    `;
    }
    /**
     * Get payment methods field HTML
     */
    getPaymentMethodsField(methods) {
        // Determine grid layout based on number of methods
        // 1 method: 1 per row (full width)
        // 2+ methods: 2 per row (with first item full-width for odd numbers > 1)
        const gridClass = methods.length === 1 ? 'grid-1' : 'grid-2';
        return `
      <div class="paycan-form-group">
        <label class="paycan-label">Payment Method</label>
        <div class="paycan-payment-methods ${gridClass}">
          ${methods.map((method, index) => {
            // For odd numbers > 1, first item spans full width
            const isOdd = methods.length % 2 !== 0 && methods.length > 1;
            const isFirstOfOdd = isOdd && index === 0;
            const fullWidthClass = isFirstOfOdd ? 'full-width' : '';
            const selectedClass = this.selectedGateway === method.key ? 'selected' : '';
            return `
              <label class="paycan-payment-method ${selectedClass} ${fullWidthClass}">
                <input
                  type="radio"
                  name="gateway"
                  value="${method.key}"
                  class="paycan-gateway-radio"
                  ${this.selectedGateway === method.key ? 'checked' : ''}
                />
                <div class="paycan-payment-method-icon">
                  <svg viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 3L4.5 8.5L2 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </div>
                <div class="paycan-payment-method-info">
                  <div class="paycan-payment-method-name">${Formatters.escapeHtml(method.name)}</div>
                  ${method.description ? `<div class="paycan-payment-method-description">${Formatters.escapeHtml(method.description)}</div>` : ''}
                </div>
              </label>
            `;
        }).join('')}
        </div>
      </div>
    `;
    }
    /**
     * Attach event listeners to modal elements
     */
    attachEventListeners() {
        if (!this.modal)
            return;
        // Close button
        const closeBtn = this.modal.querySelector('.paycan-close-btn');
        closeBtn?.addEventListener('click', () => this.close());
        // Cancel button
        const cancelBtn = this.modal.querySelector('.paycan-cancel-btn');
        cancelBtn?.addEventListener('click', () => this.close());
        // Checkout button
        const checkoutBtn = this.modal.querySelector('.paycan-checkout-btn');
        checkoutBtn?.addEventListener('click', () => this.handleCheckout());
        // Price radio buttons
        const priceRadios = this.modal.querySelectorAll('.paycan-price-radio');
        priceRadios.forEach(radio => {
            radio.addEventListener('change', (e) => {
                const target = e.target;
                this.handlePriceChange(target.value);
            });
        });
        // Price dropdown
        const priceSelect = this.modal.querySelector('.paycan-price-select');
        priceSelect?.addEventListener('change', (e) => {
            const target = e.target;
            this.handlePriceChange(target.value);
        });
        // Gateway radio buttons
        const gatewayRadios = this.modal.querySelectorAll('.paycan-gateway-radio');
        gatewayRadios.forEach(radio => {
            radio.addEventListener('change', (e) => {
                const target = e.target;
                this.selectedGateway = target.value;
                // Update UI to reflect selection
                this.modal?.querySelectorAll('.paycan-payment-method').forEach(method => {
                    method.classList.remove('selected');
                });
                target.closest('.paycan-payment-method')?.classList.add('selected');
            });
        });
    }
    /**
     * Handle price change
     */
    async handlePriceChange(priceId) {
        this.selectedPriceId = priceId;
        try {
            // Reload preview with new price
            await this.loadPreview();
            // Re-render modal
            if (this.modal) {
                this.modal.innerHTML = this.getModalContent();
                this.attachEventListeners();
            }
        }
        catch (error) {
            this.handleError(error);
        }
    }
    /**
     * Handle checkout button click
     */
    async handleCheckout() {
        if (this.loading)
            return;
        try {
            this.loading = true;
            this.updateCheckoutButton();
            // Validate email if guest
            let billingEmail;
            if (!this.sdk.isAuthenticated()) {
                const emailInput = this.modal?.querySelector('.paycan-email-input');
                if (!emailInput || !emailInput.value || !Formatters.isValidEmail(emailInput.value)) {
                    throw new Error('Please enter a valid email address');
                }
                billingEmail = emailInput.value;
            }
            // Show processing message
            this.showToast('Creating checkout session...', 'info');
            // Create checkout session
            const checkoutData = {
                product_price_id: this.selectedPriceId,
                gateway: this.selectedGateway,
                quantity: 1,
            };
            if (this.previewData) {
                checkoutData.product_id = this.previewData.product.id;
            }
            if (billingEmail) {
                checkoutData.billing_email = billingEmail;
            }
            const session = await this.sdk.checkout.create(checkoutData);
            // Show redirecting message
            this.showToast('Redirecting to gateway...', 'success');
            await new Promise(resolve => setTimeout(resolve, 1000));
            if (this.options.onSuccess) {
                this.options.onSuccess(session.checkout_url);
            }
            else {
                window.location.href = session.checkout_url;
            }
            this.close();
        }
        catch (error) {
            this.loading = false;
            this.updateCheckoutButton();
            const status = error?.status ?? error?.statusCode;
            if (status === 401 && this.sdk.logout) {
                // Force logout and re-render into guest mode
                this.sdk.logout();
                this.showToast('Session expired. Continue as guest or log in.', 'info');
                // Re-render modal content so guest email field is shown
                if (this.modal) {
                    this.modal.innerHTML = this.getModalContent();
                    this.attachEventListeners();
                }
                return;
            }
            this.handleError(error);
        }
    }
    /**
     * Update checkout button state
     */
    updateCheckoutButton() {
        const btn = this.modal?.querySelector('.paycan-checkout-btn');
        if (btn) {
            btn.disabled = this.loading;
            btn.textContent = this.loading ? 'Processing...' :
                this.previewData?.selected_price ?
                    `Pay ${Formatters.formatPrice(this.previewData.selected_price.final_price, this.previewData.selected_price.currency)}` :
                    'Continue';
        }
    }
    /**
     * Show modal with animation
     */
    show() {
        if (this.overlay && this.modal) {
            // Trigger reflow for animation
            this.overlay.offsetHeight;
            this.overlay.classList.add('paycan-show');
            this.modal.classList.add('paycan-show');
        }
    }
    /**
     * Show toast notification
     */
    showToast(message, type = 'info') {
        ToastHelper.showToast(this.modal, message, type);
    }
    /**
     * Handle errors
     */
    handleError(error) {
        console.error('[PayCan Checkout Modal]', error);
        this.showToast(error.message, 'error');
        if (this.options.onError) {
            this.options.onError(error);
        }
    }
    /**
     * Cleanup
     */
    destroy() {
        document.removeEventListener('keydown', this.handleEscapeKey);
        this.close();
    }
}

/**
 * Shared modal utility functions for PayCan SDK components
 * This module provides common modal functionality
 */
class ModalHelpers {
    /**
     * Check if dark mode is enabled
     */
    static isDarkMode() {
        if (typeof window === 'undefined')
            return false;
        // Check for explicit theme setting
        const theme = document.documentElement.getAttribute('data-theme') ||
            document.body.getAttribute('data-theme');
        if (theme === 'dark')
            return true;
        if (theme === 'light')
            return false;
        // Check for dark mode class
        if (document.documentElement.classList.contains('dark') ||
            document.body.classList.contains('dark')) {
            return true;
        }
        // Check system preference
        return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    }
    /**
     * Generate loading state HTML
     */
    static getLoadingState() {
        return `
      <div class="paycan-loading">
        <div class="paycan-spinner"></div>
        <p>Loading...</p>
      </div>
    `;
    }
    /**
     * Generate empty state HTML
     */
    static getEmptyState(message, icon) {
        return `
      <div class="paycan-empty-state">
        ${icon ? `<div class="paycan-empty-icon">${icon}</div>` : ''}
        <p class="paycan-empty-message">${message}</p>
      </div>
    `;
    }
    /**
     * Generate pagination HTML
     */
    static getPagination(currentPage, totalPages) {
        if (totalPages <= 1)
            return '';
        let pagination = '<div class="paycan-pagination">';
        // Previous button
        if (currentPage > 1) {
            pagination += `<button class="paycan-pagination-btn" data-page="${currentPage - 1}">Previous</button>`;
        }
        // Page numbers
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);
        if (startPage > 1) {
            pagination += `<button class="paycan-pagination-btn" data-page="1">1</button>`;
            if (startPage > 2) {
                pagination += '<span class="paycan-pagination-ellipsis">...</span>';
            }
        }
        for (let i = startPage; i <= endPage; i++) {
            const isActive = i === currentPage ? ' paycan-pagination-active' : '';
            pagination += `<button class="paycan-pagination-btn${isActive}" data-page="${i}">${i}</button>`;
        }
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                pagination += '<span class="paycan-pagination-ellipsis">...</span>';
            }
            pagination += `<button class="paycan-pagination-btn" data-page="${totalPages}">${totalPages}</button>`;
        }
        // Next button
        if (currentPage < totalPages) {
            pagination += `<button class="paycan-pagination-btn" data-page="${currentPage + 1}">Next</button>`;
        }
        pagination += '</div>';
        return pagination;
    }
    /**
     * Create modal container with shadow DOM
     */
    static createModalContainer(id) {
        const container = document.createElement('div');
        container.id = id;
        container.style.cssText = `
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 999999;
      pointer-events: none;
    `;
        const shadowRoot = container.attachShadow({ mode: 'closed' });
        document.body.appendChild(container);
        return { container, shadowRoot };
    }
    /**
     * Remove modal container
     */
    static removeModalContainer(container) {
        if (container && container.parentNode) {
            container.parentNode.removeChild(container);
        }
    }
}

/**
 * PayCan Products List Modal Web Component
 *
 * A framework-agnostic modal to display available products/plans
 * Integrates with checkout modal for purchasing or changing subscriptions
 */
class ProductsModal {
    constructor(sdk, options = {}) {
        this.container = null;
        this.shadowRoot = null;
        this.overlay = null;
        this.modal = null;
        this.products = [];
        this.loading = false;
        this.currentPage = 1;
        this.totalPages = 1;
        /**
         * Handle escape key press
         */
        this.handleEscapeKey = EventHandlers.createEscapeHandler(() => this.close());
        this.sdk = sdk;
        this.options = options;
    }
    /**
     * Open the modal and load products
     */
    async open() {
        // Set loading state
        this.loading = true;
        // Create modal first with loading state
        this.createModal();
        // Then load products
        try {
            await this.loadProducts();
            this.refreshModal();
        }
        catch (error) {
            this.handleError(error);
        }
    }
    /**
     * Close and destroy the modal
     */
    close() {
        if (this.container) {
            this.container.remove();
            this.container = null;
            this.shadowRoot = null;
            this.overlay = null;
            this.modal = null;
        }
        document.removeEventListener('keydown', this.handleEscapeKey);
        if (this.options.onClose) {
            this.options.onClose();
        }
    }
    /**
     * Load products from API
     */
    async loadProducts(page = 1) {
        this.loading = true;
        try {
            const params = {
                page,
                per_page: 12,
                include: 'prices',
            };
            if (this.options.type) {
                params['filter[type]'] = this.options.type;
            }
            const response = await this.sdk.products.list(params);
            this.products = response.data;
            this.currentPage = response.meta?.current_page || 1;
            this.totalPages = response.meta?.last_page || 1;
            this.loading = false;
        }
        catch (error) {
            this.loading = false;
            throw error;
        }
    }
    /**
     * Create modal DOM structure with Shadow DOM
     */
    createModal() {
        const isDark = this.isDarkMode();
        const themeClass = isDark ? 'paycan-theme-dark' : 'paycan-theme-light';
        // Create container element for Shadow DOM
        this.container = document.createElement('div');
        this.container.setAttribute('id', 'paycan-products-modal');
        // Attach Shadow DOM
        this.shadowRoot = this.container.attachShadow({ mode: 'open' });
        // Create style element inside shadow root
        const styleEl = document.createElement('style');
        styleEl.textContent = this.getStyles();
        this.shadowRoot.appendChild(styleEl);
        // Create overlay inside shadow root
        this.overlay = document.createElement('div');
        this.overlay.className = `paycan-modal-overlay ${themeClass}`;
        // Create modal container
        this.modal = document.createElement('div');
        this.modal.className = `paycan-modal paycan-modal-wide ${themeClass}`;
        this.modal.innerHTML = this.getModalContent();
        this.overlay.appendChild(this.modal);
        this.shadowRoot.appendChild(this.overlay);
        // Append container to body
        document.body.appendChild(this.container);
        // Add event listeners
        this.attachEventListeners();
        // Close on overlay click
        this.overlay.addEventListener('click', (e) => {
            if (e.target === this.overlay) {
                this.close();
            }
        });
        // Close on Escape key
        document.addEventListener('keydown', this.handleEscapeKey);
        // Show modal with animation
        setTimeout(() => {
            this.overlay?.classList.add('paycan-show');
            this.modal?.classList.add('paycan-show');
        }, 10);
    }
    /**
     * Refresh modal content
     */
    refreshModal() {
        if (this.modal) {
            this.modal.innerHTML = this.getModalContent();
            this.attachEventListeners();
        }
    }
    /**
     * Detect if dark mode should be used
     */
    isDarkMode() {
        const theme = this.options.theme || 'auto';
        if (theme === 'dark') {
            return true;
        }
        else if (theme === 'light') {
            return false;
        }
        else {
            return ModalHelpers.isDarkMode();
        }
    }
    /**
     * Get modal HTML content
     */
    getModalContent() {
        const title = this.options.subscriptionId
            ? 'Change Subscription Plan'
            : this.options.type === 'subscription'
                ? 'Available Plans'
                : 'Available Products';
        return `
      <div class="paycan-modal-header">
        <h2 class="paycan-modal-title">${title}</h2>
        <button class="paycan-close-btn" aria-label="Close">×</button>
      </div>
      <div class="paycan-toast">
        <div class="paycan-toast-content"></div>
      </div>
      <div class="paycan-modal-body">
        ${this.loading ? this.getLoadingState() : this.getProductsGrid()}
      </div>
      ${this.getPagination()}
    `;
    }
    /**
     * Get products grid HTML
     */
    getProductsGrid() {
        if (this.products.length === 0) {
            return `
        <div class="paycan-empty-state">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <p>No products available</p>
        </div>
      `;
        }
        return `
      <div class="paycan-products-grid">
        ${this.products.map(product => this.getProductCard(product)).join('')}
      </div>
    `;
    }
    /**
     * Get product card HTML
     */
    getProductCard(product) {
        const prices = product.prices || [];
        return `
      <div class="paycan-product-card">
        <div class="paycan-product-header">
          <h3 class="paycan-product-title">${Formatters.escapeHtml(product.title)}</h3>
          ${product.description ? `<p class="paycan-product-description">${Formatters.escapeHtml(Formatters.trimText(product.description, 120))}</p>` : ''}
        </div>
        ${prices.length > 0 ? `
          <div class="paycan-prices-list">
            ${prices.map((price, index) => {
            const isCurrentPrice = !!(this.options.currentPriceId &&
                price.id.toString() === this.options.currentPriceId.toString());
            const buttonClass = isCurrentPrice ? 'paycan-btn-success' : this.getPriceButtonClass(index);
            const buttonText = this.getPriceButtonText(price, isCurrentPrice);
            return `
                <div class="paycan-price-row ${isCurrentPrice ? 'current' : ''}">
                  <div class="paycan-price-name">${Formatters.escapeHtml(price.title)}</div>
                  <div class="paycan-price-amount">
                    ${Formatters.formatPrice(price.amount, price.currency)}${price.billing_period && price.billing_period !== 'once' ? '/' + Formatters.getBillingPeriodText(price.billing_period) : ''}
                  </div>
                  <button class="paycan-btn ${buttonClass} paycan-btn-sm paycan-btn-push ${isCurrentPrice ? 'current-price' : ''}"
                          data-action="select-product"
                          data-product-id="${product.id}"
                          data-price-id="${price.id}"
                          ${isCurrentPrice ? 'disabled' : ''}>
                    ${buttonText}
                  </button>
                </div>
              `;
        }).join('')}
          </div>
        ` : '<p class="paycan-help-text">No pricing available</p>'}
      </div>
    `;
    }
    /**
     * Get button text based on price type and current status
     */
    getPriceButtonText(price, isCurrentPrice) {
        if (isCurrentPrice) {
            return '✓ Current Plan';
        }
        // Check if it's a subscription (has billing period other than 'once')
        if (price.billing_period && price.billing_period !== 'once') {
            return 'Subscribe';
        }
        // One-time purchase
        return 'Buy Now';
    }
    /**
     * Get button class for price based on index
     */
    getPriceButtonClass(index) {
        const classes = ['paycan-btn-primary', 'paycan-btn-info', 'paycan-btn-warning', 'paycan-btn-purple'];
        return classes[index % classes.length];
    }
    /**
     * Get loading state HTML
     */
    getLoadingState() {
        return `
      <div class="paycan-loading-state">
        <div class="paycan-spinner"></div>
        <p>Loading products...</p>
      </div>
    `;
    }
    /**
     * Get pagination HTML
     */
    getPagination() {
        if (this.totalPages <= 1) {
            return '';
        }
        return `
      <div class="paycan-modal-footer">
        <div class="paycan-pagination">
          <button class="paycan-btn paycan-btn-secondary paycan-btn-sm" data-action="prev-page" ${this.currentPage === 1 ? 'disabled' : ''}>
            Previous
          </button>
          <span class="paycan-page-info">Page ${this.currentPage} of ${this.totalPages}</span>
          <button class="paycan-btn paycan-btn-secondary paycan-btn-sm" data-action="next-page" ${this.currentPage === this.totalPages ? 'disabled' : ''}>
            Next
          </button>
        </div>
      </div>
    `;
    }
    /**
     * Attach event listeners
     */
    attachEventListeners() {
        if (!this.modal)
            return;
        // Close button
        const closeBtn = this.modal.querySelector('.paycan-close-btn');
        closeBtn?.addEventListener('click', () => this.close());
        // Action buttons
        const actionButtons = this.modal.querySelectorAll('[data-action]');
        actionButtons.forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                const target = e.currentTarget;
                const action = target.dataset.action;
                if (!action)
                    return;
                try {
                    switch (action) {
                        case 'select-product':
                            const productId = target.dataset.productId;
                            const priceId = target.dataset.priceId;
                            if (productId && priceId) {
                                await this.handleProductSelection(productId, priceId);
                            }
                            break;
                        case 'prev-page':
                            if (this.currentPage > 1) {
                                await this.loadProducts(this.currentPage - 1);
                                this.refreshModal();
                            }
                            break;
                        case 'next-page':
                            if (this.currentPage < this.totalPages) {
                                await this.loadProducts(this.currentPage + 1);
                                this.refreshModal();
                            }
                            break;
                    }
                }
                catch (error) {
                    this.handleError(error);
                }
            });
        });
    }
    /**
     * Handle product selection - change subscription or open checkout
     */
    async handleProductSelection(productId, priceId) {
        const product = this.products.find(p => p.id.toString() === productId);
        if (!product) {
            this.showToast('Product not found', 'error');
            return;
        }
        // If we have a subscriptionId, change the subscription plan
        if (this.options.subscriptionId) {
            try {
                this.showToast('Changing subscription plan...', 'info');
                const result = await this.sdk.subscriptions.change(this.options.subscriptionId.toString(), {
                    product_price_id: priceId,
                    prorate: true,
                });
                this.showToast(result.message || 'Subscription changed successfully!', 'success');
                // Call callback if provided
                if (this.options.onProductSelected) {
                    this.options.onProductSelected(product);
                }
                // Close modal after successful change
                setTimeout(() => {
                    this.close();
                }, 1500);
            }
            catch (error) {
                this.showToast(error.message || 'Failed to change subscription', 'error');
            }
            return;
        }
        // Otherwise, open checkout modal for new purchase
        // Call callback if provided
        if (this.options.onProductSelected) {
            this.options.onProductSelected(product);
        }
        // Close products modal
        this.close();
        // Open checkout modal
        const checkoutModal = new CheckoutModal(this.sdk, {
            productId: product.id,
            priceId: priceId,
            theme: this.options.theme,
            onSuccess: (checkoutUrl) => {
                // Redirect to checkout
                window.location.href = checkoutUrl;
            },
            onError: (error) => {
                console.error('Checkout error:', error);
                // Keep checkout modal open so user can fix validation errors
                // Don't reopen products modal
            },
        });
        await checkoutModal.open();
    }
    /**
     * Show toast notification
     */
    showToast(message, type = 'info') {
        ToastHelper.showToast(this.modal, message, type);
    }
    /**
     * Handle errors
     */
    handleError(error) {
        this.showToast(error.message, 'error');
        if (this.options.onError) {
            this.options.onError(error);
        }
    }
    /**
     * Get all modal CSS styles
     */
    getStyles() {
        return `
      /* PayCan Products Modal Styles */

      ${getAllSharedStyles()}

      /* Products Modal Specific Styles */

      /* Products Grid */
      .paycan-products-grid {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
      }

      /* Product Card */
      .paycan-product-card {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        padding: 1.5rem;
        border-radius: 8px;
        border: 1px solid;
      }

      .paycan-theme-light .paycan-product-card {
        background: #ffffff;
        border-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-product-card {
        background: #1f2937;
        border-color: #374151;
      }

      /* Product Header */
      .paycan-product-header {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
      }

      .paycan-product-badge {
        display: inline-block;
        align-self: flex-start;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
      }

      .paycan-theme-light .paycan-product-badge {
        background: #f3f4f6;
        color: #374151;
      }

      .paycan-theme-dark .paycan-product-badge {
        background: #374151;
        color: #d1d5db;
      }

      .paycan-product-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0;
      }

      .paycan-theme-light .paycan-product-title {
        color: #111827;
      }

      .paycan-theme-dark .paycan-product-title {
        color: #f9fafb;
      }

      .paycan-product-description {
        font-size: 0.875rem;
        margin: 0;
        line-height: 1.5;
      }

      .paycan-theme-light .paycan-product-description {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-product-description {
        color: #9ca3af;
      }

      /* Prices List */
      .paycan-prices-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-top: 0.5rem;
      }

      /* Price Row - 3 Column Layout */
      .paycan-price-row {
        display: grid;
        grid-template-columns: 1fr auto auto;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem;
        border-radius: 6px;
        transition: background-color 0.2s ease;
      }

      .paycan-theme-light .paycan-price-row {
        background: #f9fafb;
      }

      .paycan-theme-dark .paycan-price-row {
        background: #111827;
      }

      .paycan-theme-light .paycan-price-row:hover:not(.current) {
        background: #f3f4f6;
      }

      .paycan-theme-dark .paycan-price-row:hover:not(.current) {
        background: #1f2937;
      }

      .paycan-theme-light .paycan-price-row.current {
        background: #dcfce7;
        border: 1px solid #86efac;
      }

      .paycan-theme-dark .paycan-price-row.current {
        background: #064e3b;
        border: 1px solid #065f46;
      }

      .paycan-price-name {
        font-size: 0.813rem;
        font-weight: 500;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
      }

      .paycan-theme-light .paycan-price-name {
        color: #374151;
      }

      .paycan-theme-dark .paycan-price-name {
        color: #d1d5db;
      }

      .paycan-price-amount {
        font-size: 0.813rem;
        font-weight: 600;
        white-space: nowrap;
      }

      .paycan-theme-light .paycan-price-amount {
        color: #111827;
      }

      .paycan-theme-dark .paycan-price-amount {
        color: #f9fafb;
      }

      .paycan-price-row .paycan-btn {
        min-width: 100px;
      }

      /* Responsive */
      @media (max-width: 768px) {
        .paycan-price-row {
          grid-template-columns: 1fr;
          gap: 0.5rem;
          text-align: left;
        }

        .paycan-price-name,
        .paycan-price-amount {
          font-size: 0.875rem;
        }

        .paycan-price-row .paycan-btn {
          width: 100%;
          min-width: unset;
        }
      }
    `;
    }
}

/**
 * PayCan SDK - Main Client
 *
 * Official JavaScript SDK for PayCan payment integration
 */
class PayCan {
    /**
     * Create a new PayCan instance
     *
     * @example
     * const paycan = new PayCan({
     *   apiUrl: 'https://pay.yourapp.com',
     *   debug: true
     * });
     */
    constructor(config) {
        this.validateConfig(config);
        this.http = new HttpClient(config);
        this.orders = new Orders(this.http, config);
        this.subscriptions = new Subscriptions(this.http, config);
        this.products = new Products(this.http);
        this.transactions = new Transactions(this.http);
        this.checkout = new Checkout(this.http, this.orders, this.subscriptions);
    }
    /**
     * Get current user information
     *
     * @example
     * const { user } = await paycan.me();
     */
    async me() {
        return this.http.get('/api/user/me');
    }
    /**
     * Set the PayCan user token directly
     *
     * This is the primary method for authentication. Your app should:
     * 1. Authenticate the user in YOUR system
     * 2. Call your backend to get a PayCan token for this user
     * 3. Pass the token to this method
     *
     * @example
     * // Get token from your backend however you want
     * const response = await fetch('/api/get-paycan-token');
     * const { token } = await response.json();
     *
     * // Set the token
     * paycan.setUserToken(token);
     *
     * // Now you can make authenticated requests
     * const orders = await paycan.orders.list();
     */
    setUserToken(token) {
        if (!token) {
            throw new Error('Token is required');
        }
        this.http.setToken(token);
        this.log('User token set successfully');
    }
    /**
     * Helper method to authenticate and get token from your backend
     *
     * This is optional - you can use setUserToken() instead if you prefer
     * to handle authentication yourself.
     *
     * @example
     * // Option 1: Bearer token (JWT in Authorization header)
     * await paycan.authenticate({
     *   endpoint: '/api/paycan/token',
     *   type: 'bearer',
     *   headers: { 'Authorization': `Bearer ${yourJWT}` }
     * });
     *
     * @example
     * // Option 2: Cookie-based (sends cookies automatically)
     * await paycan.authenticate({
     *   endpoint: '/api/paycan/token',
     *   type: 'cookie'
     * });
     *
     * @example
     * // Option 3: Custom authentication
     * await paycan.authenticate({
     *   endpoint: '/api/paycan/token',
     *   type: 'custom',
     *   headers: { 'X-Custom-Auth': 'your-value' }
     * });
     */
    async authenticate(config) {
        try {
            const headers = {
                'Content-Type': 'application/json',
                ...config.headers,
            };
            const fetchOptions = {
                method: 'POST',
                headers,
            };
            // Add credentials for cookie-based auth
            if (config.type === 'cookie') {
                fetchOptions.credentials = 'include';
            }
            // Add body data if provided
            if (config.data) {
                fetchOptions.body = JSON.stringify(config.data);
            }
            const response = await fetch(config.endpoint, fetchOptions);
            if (!response.ok) {
                throw new Error(`Authentication failed: ${response.statusText}`);
            }
            const data = await response.json();
            if (!data.token) {
                throw new Error('No token received from auth endpoint');
            }
            this.http.setToken(data.token);
            this.log('User authenticated successfully');
            return data;
        }
        catch (error) {
            this.log('Authentication error:', error);
            throw error;
        }
    }
    /**
     * Check if user is authenticated
     *
     * @example
     * if (paycan.isAuthenticated()) {
     *   // User is logged in
     * }
     */
    isAuthenticated() {
        return this.http.getToken() !== null;
    }
    /**
     * Get current token (useful for debugging)
     */
    getToken() {
        return this.http.getToken();
    }
    /**
     * Logout the user by clearing the authentication token
     *
     * @example
     * paycan.logout();
     */
    logout() {
        this.http.clearToken();
        // Clear resource caches
        this.orders.clearCache();
        this.subscriptions.clearCache();
        // Clear any SDK localStorage keys
        if (typeof window !== 'undefined' && typeof localStorage !== 'undefined') {
            try {
                Object.keys(localStorage).forEach((key) => {
                    if (key.startsWith('paycan_')) {
                        localStorage.removeItem(key);
                    }
                });
            }
            catch { }
        }
        this.log('User logged out');
    }
    /**
     * Make a GET request to the API
     * Exposed for use by modal components
     */
    async get(url) {
        return this.http.get(url);
    }
    /**
     * Make a POST request to the API
     * Exposed for use by modal components
     */
    async post(url, data) {
        return this.http.post(url, data);
    }
    /**
     * Open checkout modal for a product
     *
     * This will display a modal with all available prices for the product,
     * allowing the user to select a price and payment gateway before checkout.
     *
     * @example
     * // Basic usage
     * paycan.openCheckoutModal(123);
     *
     * @example
     * // With options
     * paycan.openCheckoutModal(123, {
     *   theme: 'dark',
     *   onSuccess: (checkoutUrl) => {
     *     console.log('Redirecting to:', checkoutUrl);
     *     window.location.href = checkoutUrl;
     *   },
     *   onCancel: () => {
     *     console.log('User cancelled checkout');
     *   },
     *   onError: (error) => {
     *     console.error('Checkout error:', error);
     *   }
     * });
     */
    openCheckoutModal(productId, options = {}) {
        const modalOptions = {
            productId,
            theme: options.theme || 'light',
            onSuccess: options.onSuccess,
            onCancel: options.onCancel,
            onError: options.onError,
        };
        const modal = new CheckoutModal(this, modalOptions);
        modal.open();
        this.log('Opening checkout modal for product:', productId);
        return modal;
    }
    /**
     * Open checkout modal for a specific price
     *
     * This will display a modal pre-configured for a specific price,
     * allowing the user to select a payment gateway before checkout.
     *
     * @example
     * // Basic usage
     * paycan.openCheckoutModalPrice(456);
     *
     * @example
     * // With options and callbacks
     * paycan.openCheckoutModalPrice(456, {
     *   theme: 'light',
     *   onSuccess: (checkoutUrl) => {
     *     // Custom success handler
     *     window.location.href = checkoutUrl;
     *   },
     *   onCancel: () => {
     *     console.log('Checkout cancelled');
     *   }
     * });
     */
    openCheckoutModalPrice(priceId, options = {}) {
        const modalOptions = {
            priceId,
            theme: options.theme || 'light',
            onSuccess: options.onSuccess,
            onCancel: options.onCancel,
            onError: options.onError,
        };
        const modal = new CheckoutModal(this, modalOptions);
        modal.open();
        this.log('Opening checkout modal for price:', priceId);
        return modal;
    }
    /**
     * Open products modal to browse and purchase products
     *
     * This will display a modal with all available products/plans,
     * allowing the user to browse and select a product to purchase.
     * When a product is selected, it automatically opens the checkout modal.
     *
     * @example
     * // Basic usage - show all products
     * paycan.openProductsModal();
     *
     * @example
     * // Show only subscription products
     * paycan.openProductsModal({
     *   type: 'subscription',
     *   theme: 'dark'
     * });
     *
     * @example
     * // For changing subscription plans
     * paycan.openProductsModal({
     *   type: 'subscription',
     *   currentSubscriptionId: 123,
     *   onProductSelected: (product) => {
     *     console.log('Selected product:', product);
     *   }
     * });
     */
    openProductsModal(options = {}) {
        const modalOptions = {
            theme: options.theme || 'auto',
            type: options.type,
            currentPriceId: options.currentPriceId,
            subscriptionId: options.subscriptionId,
            onClose: options.onClose,
            onError: options.onError,
            onProductSelected: options.onProductSelected,
        };
        const modal = new ProductsModal(this, modalOptions);
        modal.open();
        this.log('Opening products modal', options.type ? `for type: ${options.type}` : '');
        return modal;
    }
    /**
     * Validate configuration
     */
    validateConfig(config) {
        if (!config.apiUrl) {
            throw new Error('PayCan: apiUrl is required');
        }
        // Ensure URL doesn't have trailing slash
        config.apiUrl = config.apiUrl.replace(/\/$/, '');
    }
    /**
     * Debug logging
     */
    log(...args) {
        const config = this.http['config'];
        if (config.debug) {
            console.log('[PayCan SDK]', ...args);
        }
    }
    async refreshToken() {
        await this.http.refreshToken();
    }
}

/**
 * PayCan Subscriptions List Modal Web Component
 *
 * A framework-agnostic modal to display user subscriptions
 */
class SubscriptionsModal {
    constructor(sdk, options = {}) {
        this.container = null;
        this.shadowRoot = null;
        this.overlay = null;
        this.modal = null;
        this.subscriptions = [];
        this.loading = false;
        this.currentPage = 1;
        this.totalPages = 1;
        /**
         * Handle escape key press
         */
        this.handleEscapeKey = (e) => {
            if (e.key === 'Escape') {
                this.close();
            }
        };
        this.sdk = sdk;
        this.options = options;
    }
    /**
     * Open the modal and load subscriptions
     */
    async open() {
        try {
            await this.loadSubscriptions();
            this.createModal();
        }
        catch (error) {
            this.handleError(error);
        }
    }
    /**
     * Close and destroy the modal
     */
    close() {
        if (this.container) {
            this.container.remove();
            this.container = null;
            this.shadowRoot = null;
            this.overlay = null;
            this.modal = null;
        }
        document.removeEventListener('keydown', this.handleEscapeKey);
        if (this.options.onClose) {
            this.options.onClose();
        }
    }
    /**
     * Load subscriptions from API
     */
    async loadSubscriptions(page = 1) {
        this.loading = true;
        try {
            const response = await this.sdk.get(`/api/user/subscriptions?page=${page}&per_page=10&include=product,productPrice`);
            this.subscriptions = response.data;
            this.currentPage = response.meta?.current_page || 1;
            this.totalPages = response.meta?.last_page || 1;
            this.loading = false;
        }
        catch (error) {
            this.loading = false;
            throw error;
        }
    }
    /**
     * Cancel a subscription
     */
    async cancelSubscription(subscriptionId) {
        if (!confirm('Are you sure you want to cancel this subscription?')) {
            return;
        }
        this.loading = true;
        try {
            await this.sdk.post(`/api/user/subscriptions/${subscriptionId}/cancel`, {});
            await this.loadSubscriptions(this.currentPage);
            this.refreshModal();
            this.showToast('Subscription cancelled successfully', 'success');
        }
        catch (error) {
            this.loading = false;
            this.handleError(error);
        }
    }
    /**
     * Resume a subscription
     */
    async resumeSubscription(subscriptionId) {
        this.loading = true;
        try {
            await this.sdk.post(`/api/user/subscriptions/${subscriptionId}/resume`, {});
            await this.loadSubscriptions(this.currentPage);
            this.refreshModal();
            this.showToast('Subscription resumed successfully', 'success');
        }
        catch (error) {
            this.loading = false;
            this.handleError(error);
        }
    }
    /**
     * Handle changing subscription plan
     */
    async handleChangePlan(subscriptionId, priceId) {
        // Hide subscriptions modal (don't close, just hide)
        if (this.overlay) {
            this.overlay.style.display = 'none';
        }
        // Open products modal with subscription type and subscriptionId for changing
        const productsModal = new ProductsModal(this.sdk, {
            type: 'subscription',
            currentPriceId: priceId,
            subscriptionId: subscriptionId, // Pass subscription ID for plan change
            theme: this.options.theme,
            onProductSelected: async () => {
                // Product selected and subscription changed
                // Reload subscriptions and show the modal
                await this.loadSubscriptions();
                this.refreshModal();
                if (this.overlay) {
                    this.overlay.style.display = 'flex';
                }
            },
            onClose: () => {
                // User cancelled - show subscriptions modal again
                if (this.overlay) {
                    this.overlay.style.display = 'flex';
                }
            },
            onError: (error) => {
                console.error('Products modal error:', error);
                // Show subscriptions modal again on error
                if (this.overlay) {
                    this.overlay.style.display = 'flex';
                }
            }
        });
        await productsModal.open();
    }
    /**
     * Create modal DOM structure with Shadow DOM
     */
    createModal() {
        const isDark = this.isDarkMode();
        const themeClass = isDark ? 'paycan-theme-dark' : 'paycan-theme-light';
        // Create container element for Shadow DOM
        this.container = document.createElement('div');
        this.container.setAttribute('id', 'paycan-subscriptions-modal');
        // Attach Shadow DOM
        this.shadowRoot = this.container.attachShadow({ mode: 'open' });
        // Create style element inside shadow root
        const styleEl = document.createElement('style');
        styleEl.textContent = this.getStyles();
        this.shadowRoot.appendChild(styleEl);
        // Create overlay inside shadow root
        this.overlay = document.createElement('div');
        this.overlay.className = `paycan-modal-overlay ${themeClass}`;
        // Create modal container
        this.modal = document.createElement('div');
        this.modal.className = `paycan-modal paycan-modal-wide ${themeClass}`;
        this.modal.innerHTML = this.getModalContent();
        this.overlay.appendChild(this.modal);
        this.shadowRoot.appendChild(this.overlay);
        // Append container to body
        document.body.appendChild(this.container);
        // Add event listeners
        this.attachEventListeners();
        // Close on overlay click
        this.overlay.addEventListener('click', (e) => {
            if (e.target === this.overlay) {
                this.close();
            }
        });
        // Close on Escape key
        document.addEventListener('keydown', this.handleEscapeKey);
    }
    /**
     * Refresh modal content
     */
    refreshModal() {
        if (this.modal) {
            this.modal.innerHTML = this.getModalContent();
            this.attachEventListeners();
        }
    }
    /**
     * Detect if dark mode should be used
     */
    isDarkMode() {
        const theme = this.options.theme || 'auto';
        if (theme === 'dark') {
            return true;
        }
        else if (theme === 'light') {
            return false;
        }
        else {
            return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        }
    }
    /**
     * Get modal HTML content
     */
    getModalContent() {
        return `
      <div class="paycan-modal-header">
        <h2 class="paycan-modal-title">My Subscriptions</h2>
        <button class="paycan-close-btn" aria-label="Close">×</button>
      </div>
      <div class="paycan-toast">
        <div class="paycan-toast-content"></div>
      </div>
      <div class="paycan-modal-body">
        <div class="paycan-body-header">
          <a href="#" class="paycan-link" data-action="view-transactions">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Payment History
          </a>
        </div>
        ${this.loading ? this.getLoadingState() : this.getSubscriptionsList()}
      </div>
      ${this.getPagination()}
    `;
    }
    /**
     * Get subscriptions list HTML
     */
    getSubscriptionsList() {
        if (this.subscriptions.length === 0) {
            return `
        <div class="paycan-empty-state">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <p>No subscriptions found</p>
        </div>
      `;
        }
        return `
      <div class="paycan-items-grid">
        ${this.subscriptions.map(sub => this.getSubscriptionCard(sub)).join('')}
      </div>
    `;
    }
    /**
     * Get subscription card HTML
     */
    getSubscriptionCard(sub) {
        const statusClass = this.getStatusClass(sub.status);
        const statusText = this.formatStatus(sub.status);
        const actions = this.getSubscriptionActions(sub);
        const periodEnd = sub.current_period_end || sub.next_billing_date || sub.ends_at;
        return `
      <div class="paycan-card">
        <div class="paycan-card-header">
          <div>
            <h3 class="paycan-card-title">${this.escapeHtml(sub.product.name)}</h3>
            <p class="paycan-card-subtitle">${this.escapeHtml(sub.product_price.title)}</p>
          </div>
          <span class="paycan-badge paycan-badge-${statusClass}">${statusText}</span>
        </div>
        <div class="paycan-card-body">
          <div class="paycan-info-grid">
            <div class="paycan-info-item">
              <span class="paycan-info-label">Amount</span>
              <span class="paycan-info-value">${this.formatPrice(sub.product_price.amount, sub.product_price.currency)} / ${this.getBillingPeriodText(sub.product_price.billing_period)}</span>
            </div>
            <div class="paycan-info-item">
              <span class="paycan-info-label">Started</span>
              <span class="paycan-info-value">${this.formatDate(sub.created_at)}</span>
            </div>
            ${periodEnd ? `
              <div class="paycan-info-item">
                <span class="paycan-info-label">${sub.status === 'active' ? 'Next Payment' : 'Ends On'}</span>
                <span class="paycan-info-value">${this.formatDate(periodEnd)}</span>
              </div>
            ` : ''}
            ${sub.trial_ends_at && new Date(sub.trial_ends_at) > new Date() ? `
              <div class="paycan-info-item">
                <span class="paycan-info-label">Trial Ends</span>
                <span class="paycan-info-value">${this.formatDate(sub.trial_ends_at)}</span>
              </div>
            ` : ''}
            <div class="paycan-info-item">
              <span class="paycan-info-label">Payment Method</span>
              <span class="paycan-info-value">${this.formatGateway(sub.gateway)}</span>
            </div>
          </div>
        </div>
        ${actions ? `<div class="paycan-card-footer">${actions}</div>` : ''}
      </div>
    `;
    }
    /**
     * Get subscription action buttons
     */
    getSubscriptionActions(sub) {
        if (sub.status === 'canceled') {
            // Canceled subscriptions can be resumed if not expired or allowed by can_resume
            if ((sub.can_resume === true) ||
                (sub.ends_at && new Date(sub.ends_at) > new Date())) {
                return `
          <button class="paycan-btn paycan-btn-primary paycan-btn-sm" data-action="resume" data-id="${sub.id}">
            Resume Subscription
          </button>
        `;
            }
            return '';
        }
        if (sub.status === 'active' || sub.status === 'trialing') {
            return `
        <button class="paycan-btn paycan-btn-primary paycan-btn-sm paycan-btn-push" data-action="change-plan" data-id="${sub.id}" data-price-id="${sub.product_price?.id || ''}">
          Change Plan
        </button>
        <button class="paycan-btn paycan-btn-secondary paycan-btn-sm" data-action="change-payment" data-id="${sub.id}">
          Change Payment
        </button>
        <button class="paycan-btn paycan-btn-danger paycan-btn-sm" data-action="cancel" data-id="${sub.id}">
          Cancel Subscription
        </button>
      `;
        }
        return '';
    }
    /**
     * Get loading state HTML
     */
    getLoadingState() {
        return `
      <div class="paycan-loading-state">
        <div class="paycan-spinner"></div>
        <p>Loading...</p>
      </div>
    `;
    }
    /**
     * Get pagination HTML
     */
    getPagination() {
        if (this.totalPages <= 1) {
            return '';
        }
        return `
      <div class="paycan-modal-footer">
        <div class="paycan-pagination">
          <button class="paycan-btn paycan-btn-secondary paycan-btn-sm" data-action="prev-page" ${this.currentPage === 1 ? 'disabled' : ''}>
            Previous
          </button>
          <span class="paycan-page-info">Page ${this.currentPage} of ${this.totalPages}</span>
          <button class="paycan-btn paycan-btn-secondary paycan-btn-sm" data-action="next-page" ${this.currentPage === this.totalPages ? 'disabled' : ''}>
            Next
          </button>
        </div>
      </div>
    `;
    }
    /**
     * Attach event listeners
     */
    attachEventListeners() {
        if (!this.modal)
            return;
        // Close button
        const closeBtn = this.modal.querySelector('.paycan-close-btn');
        closeBtn?.addEventListener('click', () => this.close());
        // Action buttons and links
        const actionButtons = this.modal.querySelectorAll('[data-action]');
        actionButtons.forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                const target = e.currentTarget;
                const action = target.dataset.action;
                const id = target.dataset.id;
                if (!action)
                    return;
                try {
                    switch (action) {
                        case 'cancel':
                            if (id)
                                await this.cancelSubscription(id);
                            break;
                        case 'resume':
                            if (id)
                                await this.resumeSubscription(id);
                            break;
                        case 'change-plan':
                            if (id)
                                await this.handleChangePlan(id, target.dataset.priceId);
                            break;
                        case 'change-payment':
                            this.showToast('Change payment feature coming soon', 'info');
                            break;
                        case 'view-transactions':
                            // Open transactions modal
                            const { TransactionsModal } = await Promise.resolve().then(function () { return transactionsModal; });
                            const txModal = new TransactionsModal(this.sdk, this.options);
                            txModal.open();
                            break;
                        case 'prev-page':
                            if (this.currentPage > 1) {
                                await this.loadSubscriptions(this.currentPage - 1);
                                this.refreshModal();
                            }
                            break;
                        case 'next-page':
                            if (this.currentPage < this.totalPages) {
                                await this.loadSubscriptions(this.currentPage + 1);
                                this.refreshModal();
                            }
                            break;
                    }
                }
                catch (error) {
                    this.handleError(error);
                }
            });
        });
    }
    /**
     * Show toast notification
     */
    showToast(message, type = 'info') {
        ToastHelper.showToast(this.modal, message, type);
    }
    /**
     * Handle errors
     */
    handleError(error) {
        this.showToast(error.message, 'error');
        if (this.options.onError) {
            this.options.onError(error);
        }
    }
    /**
     * Format price
     */
    formatPrice(amount, currency) {
        if (!currency) {
            return amount.toFixed(2);
        }
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: currency.toUpperCase(),
        }).format(amount);
    }
    /**
     * Format date
     */
    formatDate(date) {
        return new Date(date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    }
    /**
     * Format billing period
     */
    getBillingPeriodText(period) {
        const periodMap = {
            'day': 'day',
            'week': 'week',
            'month': 'month',
            'year': 'year',
        };
        return periodMap[period] || period;
    }
    /**
     * Format status
     */
    formatStatus(status) {
        return status.charAt(0).toUpperCase() + status.slice(1).replace(/_/g, ' ');
    }
    /**
     * Get status class
     */
    getStatusClass(status) {
        const statusMap = {
            'active': 'success',
            'completed': 'success',
            'succeeded': 'success',
            'pending': 'warning',
            'processing': 'warning',
            'canceled': 'error',
            'cancelled': 'error',
            'failed': 'error',
            'refunded': 'info',
        };
        return statusMap[status] || 'default';
    }
    /**
     * Format gateway name
     */
    formatGateway(gateway) {
        const gatewayMap = {
            'stripe': 'Stripe',
            'paypal': 'PayPal',
        };
        return gatewayMap[gateway] || gateway;
    }
    /**
     * Escape HTML to prevent XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    /**
     * Get all modal CSS styles
     */
    getStyles() {
        return `
      /* PayCan Subscriptions Modal Styles */

      /* CSS Variables for Customization */
      .paycan-modal-overlay {
        --paycan-accent: #3b82f6;
        --paycan-accent-hover: #2563eb;
        --paycan-accent-light: #eff6ff;
        --paycan-accent-dark: #1e3a8a;
      }

      /* Overlay */
      .paycan-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 999999;
        padding: 1rem;
      }

      /* Modal Container */
      .paycan-modal {
        width: 100%;
        max-width: 600px;
        max-height: 90vh;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
      }

      .paycan-modal-wide {
        max-width: 800px;
      }

      /* Theme Colors */
      .paycan-modal.paycan-theme-light {
        background: #f9fafb;
        color: #111827;
      }

      .paycan-modal.paycan-theme-dark {
        background: #111827;
        color: #f9fafb;
      }

      /* Modal Header */
      .paycan-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid;
        flex-shrink: 0;
      }

      .paycan-theme-light .paycan-modal-header {
        border-bottom-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-modal-header {
        border-bottom-color: #374151;
      }

      .paycan-modal-title {
        font-size: 1.25rem;
        font-weight: 500;
        margin: 0;
      }

      .paycan-header-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
      }

      .paycan-link {
        font-size: 0.875rem;
        text-decoration: none;
        cursor: pointer;
        transition: opacity 0.2s;
      }

      .paycan-link:hover {
        opacity: 0.8;
      }

      .paycan-theme-light .paycan-link {
        color: #3b82f6;
      }

      .paycan-theme-dark .paycan-link {
        color: #60a5fa;
      }

      /* Close Button */
      .paycan-close-btn {
        background: none;
        border: none;
        font-size: 1.5rem;
        font-weight: 200;
        cursor: pointer;
        padding: 0;
        line-height: 1;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: background 0.15s;
        box-shadow: none;
      }

      .paycan-theme-light .paycan-close-btn {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-close-btn {
        color: #9ca3af;
      }

      .paycan-theme-light .paycan-close-btn:hover {
        background: #f3f4f6;
      }

      .paycan-theme-dark .paycan-close-btn:hover {
        background: #374151;
      }

      /* Back Button */
      .paycan-back-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        font-weight: 400;
        border-radius: 4px;
        transition: background 0.15s;
      }

      .paycan-theme-light .paycan-back-btn {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-back-btn {
        color: #9ca3af;
      }

      .paycan-theme-light .paycan-back-btn:hover {
        background: #f3f4f6;
      }

      .paycan-theme-dark .paycan-back-btn:hover {
        background: #374151;
      }

      /* Modal Body */
      .paycan-modal-body {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
      }

      .paycan-body-header {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 1rem;
      }

      .paycan-body-header .paycan-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
      }

      .paycan-body-header .paycan-link svg {
        flex-shrink: 0;
      }

      /* Modal Footer */
      .paycan-modal-footer {
        padding: 1rem 1.25rem;
        border-top: 1px solid;
        flex-shrink: 0;
      }

      .paycan-theme-light .paycan-modal-footer {
        border-top-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-modal-footer {
        border-top-color: #374151;
      }

      /* Items Grid */
      .paycan-items-grid {
        display: grid;
        gap: 1rem;
      }

      /* Card */
      .paycan-card {
        border-radius: 8px;
        border: 1px solid;
        overflow: hidden;
      }

      .paycan-theme-light .paycan-card {
        background: #ffffff;
        border-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-card {
        background: #1f2937;
        border-color: #4b5563;
      }

      .paycan-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding: 1rem;
        gap: 1rem;
        border-bottom: 1px solid;
      }

      .paycan-theme-light .paycan-card-header {
        border-bottom-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-card-header {
        border-bottom-color: #374151;
      }

      .paycan-card-title {
        font-size: 1rem;
        font-weight: 500;
        margin: 0 0 0.25rem 0;
      }

      .paycan-card-subtitle {
        font-size: 0.875rem;
        margin: 0;
      }

      .paycan-theme-light .paycan-card-subtitle {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-card-subtitle {
        color: #9ca3af;
      }

      .paycan-card-body {
        padding: 1rem;
      }

      .paycan-card-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.5rem;
        padding: 1rem;
        border-top: 1px solid;
      }

      .paycan-theme-light .paycan-card-footer {
        border-top-color: #e5e7eb;
        background: #f9fafb;
      }

      .paycan-theme-dark .paycan-card-footer {
        border-top-color: #374151;
        background: #111827;
      }

      /* Info Grid */
      .paycan-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 0.75rem;
      }

      .paycan-info-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
      }

      .paycan-info-label {
        font-size: 0.75rem;
        font-weight: 400;
      }

      .paycan-theme-light .paycan-info-label {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-info-label {
        color: #9ca3af;
      }

      .paycan-info-value {
        font-size: 0.875rem;
        font-weight: 400;
      }

      .paycan-info-warning .paycan-info-label,
      .paycan-info-warning .paycan-info-value {
        color: #f59e0b;
      }

      /* Badge */
      .paycan-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-weight: 500;
        flex-shrink: 0;
      }

      .paycan-badge-success {
        background: #d1fae5;
        color: #065f46;
      }

      .paycan-theme-dark .paycan-badge-success {
        background: #064e3b;
        color: #6ee7b7;
      }

      .paycan-badge-warning {
        background: #fef3c7;
        color: #92400e;
      }

      .paycan-theme-dark .paycan-badge-warning {
        background: #78350f;
        color: #fcd34d;
      }

      .paycan-badge-error {
        background: #fee2e2;
        color: #991b1b;
      }

      .paycan-theme-dark .paycan-badge-error {
        background: #7f1d1d;
        color: #fca5a5;
      }

      .paycan-badge-info {
        background: #dbeafe;
        color: #1e40af;
      }

      .paycan-theme-dark .paycan-badge-info {
        background: #1e3a8a;
        color: #93c5fd;
      }

      /* Transactions List */
      .paycan-transactions-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
      }

      .paycan-transaction-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid;
      }

      .paycan-theme-light .paycan-transaction-row {
        background: #ffffff;
        border-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-transaction-row {
        background: #1f2937;
        border-color: #4b5563;
      }

      .paycan-transaction-info {
        flex: 1;
        min-width: 0;
      }

      .paycan-transaction-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.5rem;
      }

      .paycan-transaction-number {
        font-size: 0.875rem;
        font-weight: 500;
      }

      .paycan-transaction-meta {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.75rem;
      }

      .paycan-theme-light .paycan-transaction-meta {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-transaction-meta {
        color: #9ca3af;
      }

      .paycan-transaction-description {
        font-size: 0.875rem;
        margin: 0.5rem 0 0 0;
      }

      .paycan-theme-light .paycan-transaction-description {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-transaction-description {
        color: #9ca3af;
      }

      .paycan-transaction-amount {
        font-size: 1rem;
        font-weight: 500;
        flex-shrink: 0;
      }

      /* Buttons */
      .paycan-btn {
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 400;
        cursor: pointer;
        border: 1px solid transparent;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
      }

      .paycan-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
      }

      .paycan-btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.8125rem;
      }

      .paycan-btn-primary {
        background: var(--paycan-accent);
        color: white;
      }

      .paycan-btn-primary:hover:not(:disabled) {
        background: var(--paycan-accent-hover);
      }

      .paycan-btn-secondary {
        border-color: #d1d5db;
      }

      .paycan-theme-light .paycan-btn-secondary {
        background: #ffffff;
        color: #374151;
      }

      .paycan-theme-dark .paycan-btn-secondary {
        background: #374151;
        color: #f9fafb;
        border-color: #4b5563;
      }

      .paycan-theme-light .paycan-btn-secondary:hover:not(:disabled) {
        background: #f9fafb;
      }

      .paycan-theme-dark .paycan-btn-secondary:hover:not(:disabled) {
        background: #4b5563;
      }

      .paycan-btn-danger {
        background: #ef4444;
        color: white;
      }

      .paycan-btn-danger:hover:not(:disabled) {
        background: #dc2626;
      }

      /* Empty State */
      .paycan-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem 1rem;
        text-align: center;
      }

      .paycan-empty-state svg {
        margin-bottom: 1rem;
      }

      .paycan-theme-light .paycan-empty-state svg {
        color: #9ca3af;
      }

      .paycan-theme-dark .paycan-empty-state svg {
        color: #6b7280;
      }

      .paycan-empty-state p {
        font-size: 0.875rem;
        margin: 0;
      }

      .paycan-theme-light .paycan-empty-state p {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-empty-state p {
        color: #9ca3af;
      }

      /* Loading State */
      .paycan-loading-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem 1rem;
      }

      .paycan-spinner {
        width: 40px;
        height: 40px;
        border: 3px solid;
        border-radius: 50%;
        border-top-color: var(--paycan-accent);
        animation: paycan-spin 0.6s linear infinite;
      }

      .paycan-theme-light .paycan-spinner {
        border-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-spinner {
        border-color: #374151;
      }

      @keyframes paycan-spin {
        to { transform: rotate(360deg); }
      }

      .paycan-loading-state p {
        margin-top: 1rem;
        font-size: 0.875rem;
      }

      .paycan-theme-light .paycan-loading-state p {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-loading-state p {
        color: #9ca3af;
      }

      /* Pagination */
      .paycan-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
      }

      .paycan-page-info {
        font-size: 0.875rem;
      }

      .paycan-theme-light .paycan-page-info {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-page-info {
        color: #9ca3af;
      }

      /* Toast Notification */
      .paycan-toast {
        position: absolute;
        top: 5rem;
        left: 50%;
        transform: translateX(-50%) translateY(-100px);
        padding: 0.75rem 1rem;
        border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        opacity: 0;
        transition: all 0.3s;
        pointer-events: none;
        z-index: 1000;
        max-width: 90%;
      }

      .paycan-toast.show {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
      }

      .paycan-toast-content {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
      }

      .paycan-toast-success {
        background: #10b981;
        color: white;
      }

      .paycan-toast-error {
        background: #ef4444;
        color: white;
      }

      .paycan-toast-info {
        background: #3b82f6;
        color: white;
      }

      /* Responsive */
      @media (max-width: 640px) {
        .paycan-modal {
          max-width: 100%;
        }

        .paycan-info-grid {
          grid-template-columns: 1fr;
        }

        .paycan-transaction-row {
          flex-direction: column;
          align-items: flex-start;
        }

        .paycan-transaction-amount {
          align-self: flex-end;
        }
      }
    `;
    }
}

/**
 * PayCan Orders List Modal Web Component
 *
 * A framework-agnostic modal to display user orders
 */
class OrdersModal {
    constructor(sdk, options = {}) {
        this.container = null;
        this.shadowRoot = null;
        this.overlay = null;
        this.modal = null;
        this.orders = [];
        this.loading = false;
        this.currentPage = 1;
        this.totalPages = 1;
        /**
         * Handle escape key press
         */
        this.handleEscapeKey = (e) => {
            if (e.key === 'Escape') {
                this.close();
            }
        };
        this.sdk = sdk;
        this.options = options;
    }
    /**
     * Open the modal and load orders
     */
    async open() {
        try {
            await this.loadOrders();
            this.createModal();
        }
        catch (error) {
            this.handleError(error);
        }
    }
    /**
     * Close and destroy the modal
     */
    close() {
        if (this.container) {
            this.container.remove();
            this.container = null;
            this.shadowRoot = null;
            this.overlay = null;
            this.modal = null;
        }
        document.removeEventListener('keydown', this.handleEscapeKey);
        if (this.options.onClose) {
            this.options.onClose();
        }
    }
    /**
     * Load orders from API
     */
    async loadOrders(page = 1) {
        this.loading = true;
        try {
            const response = await this.sdk.get(`/api/user/orders?page=${page}&per_page=10&include=product,productPrice`);
            this.orders = response.data;
            this.currentPage = response.meta?.current_page || 1;
            this.totalPages = response.meta?.last_page || 1;
            this.loading = false;
        }
        catch (error) {
            this.loading = false;
            throw error;
        }
    }
    /**
     * Create modal DOM structure with Shadow DOM
     */
    createModal() {
        const isDark = this.isDarkMode();
        const themeClass = isDark ? 'paycan-theme-dark' : 'paycan-theme-light';
        // Create container element for Shadow DOM
        this.container = document.createElement('div');
        this.container.setAttribute('id', 'paycan-orders-modal');
        // Attach Shadow DOM
        this.shadowRoot = this.container.attachShadow({ mode: 'open' });
        // Create style element inside shadow root
        const styleEl = document.createElement('style');
        styleEl.textContent = this.getStyles();
        this.shadowRoot.appendChild(styleEl);
        // Create overlay inside shadow root
        this.overlay = document.createElement('div');
        this.overlay.className = `paycan-modal-overlay ${themeClass}`;
        // Create modal container
        this.modal = document.createElement('div');
        this.modal.className = `paycan-modal paycan-modal-wide ${themeClass}`;
        this.modal.innerHTML = this.getModalContent();
        this.overlay.appendChild(this.modal);
        this.shadowRoot.appendChild(this.overlay);
        // Append container to body
        document.body.appendChild(this.container);
        // Add event listeners
        this.attachEventListeners();
        // Close on overlay click
        this.overlay.addEventListener('click', (e) => {
            if (e.target === this.overlay) {
                this.close();
            }
        });
        // Close on Escape key
        document.addEventListener('keydown', this.handleEscapeKey);
    }
    /**
     * Refresh modal content
     */
    refreshModal() {
        if (this.modal) {
            this.modal.innerHTML = this.getModalContent();
            this.attachEventListeners();
        }
    }
    /**
     * Detect if dark mode should be used
     */
    isDarkMode() {
        const theme = this.options.theme || 'auto';
        if (theme === 'dark') {
            return true;
        }
        else if (theme === 'light') {
            return false;
        }
        else {
            return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        }
    }
    /**
     * Get modal HTML content
     */
    getModalContent() {
        return `
      <div class="paycan-modal-header">
        <h2 class="paycan-modal-title">My Orders</h2>
        <button class="paycan-close-btn" aria-label="Close">×</button>
      </div>
      <div class="paycan-toast">
        <div class="paycan-toast-content"></div>
      </div>
      <div class="paycan-modal-body">
        <div class="paycan-body-header">
          <a href="#" class="paycan-link" data-action="view-transactions">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Payment History
          </a>
        </div>
        ${this.loading ? this.getLoadingState() : this.getOrdersList()}
      </div>
      ${this.getPagination()}
    `;
    }
    /**
     * Get orders list HTML
     */
    getOrdersList() {
        if (this.orders.length === 0) {
            return `
        <div class="paycan-empty-state">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <p>No orders found</p>
        </div>
      `;
        }
        return `
      <div class="paycan-items-grid">
        ${this.orders.map(sub => this.getOrderCard(sub)).join('')}
      </div>
    `;
    }
    /**
     * Get subscription card HTML
     */
    getOrderCard(order) {
        const statusClass = this.getStatusClass(order.status);
        const statusText = this.formatStatus(order.status);
        return `
      <div class="paycan-card">
        <div class="paycan-card-header">
          <div>
            <h3 class="paycan-card-title">${this.escapeHtml(order.product.name)}</h3>
            <p class="paycan-card-subtitle">${this.escapeHtml(order.product_price.title)}</p>
          </div>
          <span class="paycan-badge paycan-badge-${statusClass}">${statusText}</span>
        </div>
        <div class="paycan-card-body">
          <div class="paycan-info-grid">
            <div class="paycan-info-item">
              <span class="paycan-info-label">Total</span>
              <span class="paycan-info-value">${this.formatPrice(order.total, order.currency)}</span>
            </div>
            <div class="paycan-info-item">
              <span class="paycan-info-label">Order Number</span>
              <span class="paycan-info-value">${this.escapeHtml(order.order_number)}</span>
            </div>
            <div class="paycan-info-item">
              <span class="paycan-info-label">Payment Method</span>
              <span class="paycan-info-value">${this.formatGateway(order.gateway)}</span>
            </div>
            <div class="paycan-info-item">
              <span class="paycan-info-label">Quantity</span>
              <span class="paycan-info-value">${order.quantity}</span>
            </div>
            <div class="paycan-info-item">
              <span class="paycan-info-label">Order Date</span>
              <span class="paycan-info-value">${this.formatDate(order.created_at)}</span>
            </div>
            <div class="paycan-info-item">
              <span class="paycan-info-label">Billing Period</span>
              <span class="paycan-info-value">${this.getBillingPeriodText(order.product_price.billing_period)}</span>
            </div>
          </div>
        </div>
      </div>
    `;
    }
    /**
     * Get loading state HTML
     */
    getLoadingState() {
        return `
      <div class="paycan-loading-state">
        <div class="paycan-spinner"></div>
        <p>Loading...</p>
      </div>
    `;
    }
    /**
     * Get pagination HTML
     */
    getPagination() {
        if (this.totalPages <= 1) {
            return '';
        }
        return `
      <div class="paycan-modal-footer">
        <div class="paycan-pagination">
          <button class="paycan-btn paycan-btn-secondary paycan-btn-sm" data-action="prev-page" ${this.currentPage === 1 ? 'disabled' : ''}>
            Previous
          </button>
          <span class="paycan-page-info">Page ${this.currentPage} of ${this.totalPages}</span>
          <button class="paycan-btn paycan-btn-secondary paycan-btn-sm" data-action="next-page" ${this.currentPage === this.totalPages ? 'disabled' : ''}>
            Next
          </button>
        </div>
      </div>
    `;
    }
    /**
     * Attach event listeners
     */
    attachEventListeners() {
        if (!this.modal)
            return;
        // Close button
        const closeBtn = this.modal.querySelector('.paycan-close-btn');
        closeBtn?.addEventListener('click', () => this.close());
        // Action buttons and links
        const actionButtons = this.modal.querySelectorAll('[data-action]');
        actionButtons.forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                const target = e.currentTarget;
                const action = target.dataset.action;
                if (!action)
                    return;
                try {
                    switch (action) {
                        case 'view-transactions':
                            // Open transactions modal
                            const { TransactionsModal } = await Promise.resolve().then(function () { return transactionsModal; });
                            const txModal = new TransactionsModal(this.sdk, this.options);
                            txModal.open();
                            break;
                        case 'prev-page':
                            if (this.currentPage > 1) {
                                await this.loadOrders(this.currentPage - 1);
                                this.refreshModal();
                            }
                            break;
                        case 'next-page':
                            if (this.currentPage < this.totalPages) {
                                await this.loadOrders(this.currentPage + 1);
                                this.refreshModal();
                            }
                            break;
                    }
                }
                catch (error) {
                    this.handleError(error);
                }
            });
        });
    }
    /**
     * Show toast notification
     */
    showToast(message, type = 'info') {
        ToastHelper.showToast(this.modal, message, type);
    }
    /**
     * Handle errors
     */
    handleError(error) {
        this.showToast(error.message, 'error');
        if (this.options.onError) {
            this.options.onError(error);
        }
    }
    /**
     * Format price
     */
    formatPrice(amount, currency) {
        if (!currency) {
            return amount.toFixed(2);
        }
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: currency.toUpperCase(),
        }).format(amount);
    }
    /**
     * Format date
     */
    formatDate(date) {
        return new Date(date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    }
    /**
     * Format billing period
     */
    getBillingPeriodText(period) {
        const periodMap = {
            'day': 'day',
            'week': 'week',
            'month': 'month',
            'year': 'year',
        };
        return periodMap[period] || period;
    }
    /**
     * Format status
     */
    formatStatus(status) {
        return status.charAt(0).toUpperCase() + status.slice(1).replace(/_/g, ' ');
    }
    /**
     * Get status class
     */
    getStatusClass(status) {
        const statusMap = {
            'active': 'success',
            'completed': 'success',
            'succeeded': 'success',
            'pending': 'warning',
            'processing': 'warning',
            'cancelled': 'error',
            'failed': 'error',
            'refunded': 'info',
        };
        return statusMap[status] || 'default';
    }
    /**
     * Format gateway name
     */
    formatGateway(gateway) {
        const gatewayMap = {
            'stripe': 'Stripe',
            'paypal': 'PayPal',
        };
        return gatewayMap[gateway] || gateway;
    }
    /**
     * Escape HTML to prevent XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    /**
     * Get all modal CSS styles
     */
    getStyles() {
        return `
      /* PayCan Orders Modal Styles */

      /* CSS Variables for Customization */
      .paycan-modal-overlay {
        --paycan-accent: #3b82f6;
        --paycan-accent-hover: #2563eb;
        --paycan-accent-light: #eff6ff;
        --paycan-accent-dark: #1e3a8a;
      }

      /* Overlay */
      .paycan-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 999999;
        padding: 1rem;
      }

      /* Modal Container */
      .paycan-modal {
        width: 100%;
        max-width: 600px;
        max-height: 90vh;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
      }

      .paycan-modal-wide {
        max-width: 800px;
      }

      /* Theme Colors */
      .paycan-modal.paycan-theme-light {
        background: #f9fafb;
        color: #111827;
      }

      .paycan-modal.paycan-theme-dark {
        background: #111827;
        color: #f9fafb;
      }

      /* Modal Header */
      .paycan-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid;
        flex-shrink: 0;
      }

      .paycan-theme-light .paycan-modal-header {
        border-bottom-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-modal-header {
        border-bottom-color: #374151;
      }

      .paycan-modal-title {
        font-size: 1.25rem;
        font-weight: 500;
        margin: 0;
      }

      .paycan-header-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
      }

      .paycan-link {
        font-size: 0.875rem;
        text-decoration: none;
        cursor: pointer;
        transition: opacity 0.2s;
      }

      .paycan-link:hover {
        opacity: 0.8;
      }

      .paycan-theme-light .paycan-link {
        color: #3b82f6;
      }

      .paycan-theme-dark .paycan-link {
        color: #60a5fa;
      }

      /* Close Button */
      .paycan-close-btn {
        background: none;
        border: none;
        font-size: 1.5rem;
        font-weight: 200;
        cursor: pointer;
        padding: 0;
        line-height: 1;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: background 0.15s;
        box-shadow: none;
      }

      .paycan-theme-light .paycan-close-btn {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-close-btn {
        color: #9ca3af;
      }

      .paycan-theme-light .paycan-close-btn:hover {
        background: #f3f4f6;
      }

      .paycan-theme-dark .paycan-close-btn:hover {
        background: #374151;
      }

      /* Back Button */
      .paycan-back-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        font-weight: 400;
        border-radius: 4px;
        transition: background 0.15s;
      }

      .paycan-theme-light .paycan-back-btn {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-back-btn {
        color: #9ca3af;
      }

      .paycan-theme-light .paycan-back-btn:hover {
        background: #f3f4f6;
      }

      .paycan-theme-dark .paycan-back-btn:hover {
        background: #374151;
      }

      /* Modal Body */
      .paycan-modal-body {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
      }

      .paycan-body-header {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 1rem;
      }

      .paycan-body-header .paycan-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
      }

      .paycan-body-header .paycan-link svg {
        flex-shrink: 0;
      }

      /* Modal Footer */
      .paycan-modal-footer {
        padding: 1rem 1.25rem;
        border-top: 1px solid;
        flex-shrink: 0;
      }

      .paycan-theme-light .paycan-modal-footer {
        border-top-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-modal-footer {
        border-top-color: #374151;
      }

      /* Items Grid */
      .paycan-items-grid {
        display: grid;
        gap: 1rem;
      }

      /* Card */
      .paycan-card {
        border-radius: 8px;
        border: 1px solid;
        overflow: hidden;
      }

      .paycan-theme-light .paycan-card {
        background: #ffffff;
        border-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-card {
        background: #1f2937;
        border-color: #4b5563;
      }

      .paycan-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding: 1rem;
        gap: 1rem;
        border-bottom: 1px solid;
      }

      .paycan-theme-light .paycan-card-header {
        border-bottom-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-card-header {
        border-bottom-color: #374151;
      }

      .paycan-card-title {
        font-size: 1rem;
        font-weight: 500;
        margin: 0 0 0.25rem 0;
      }

      .paycan-card-subtitle {
        font-size: 0.875rem;
        margin: 0;
      }

      .paycan-theme-light .paycan-card-subtitle {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-card-subtitle {
        color: #9ca3af;
      }

      .paycan-card-body {
        padding: 1rem;
      }

      .paycan-card-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.5rem;
        padding: 1rem;
        border-top: 1px solid;
      }

      .paycan-theme-light .paycan-card-footer {
        border-top-color: #e5e7eb;
        background: #f9fafb;
      }

      .paycan-theme-dark .paycan-card-footer {
        border-top-color: #374151;
        background: #111827;
      }

      /* Info Grid */
      .paycan-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 0.75rem;
      }

      .paycan-info-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
      }

      .paycan-info-label {
        font-size: 0.75rem;
        font-weight: 400;
      }

      .paycan-theme-light .paycan-info-label {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-info-label {
        color: #9ca3af;
      }

      .paycan-info-value {
        font-size: 0.875rem;
        font-weight: 400;
      }

      .paycan-info-warning .paycan-info-label,
      .paycan-info-warning .paycan-info-value {
        color: #f59e0b;
      }

      /* Badge */
      .paycan-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-weight: 500;
        flex-shrink: 0;
      }

      .paycan-badge-success {
        background: #d1fae5;
        color: #065f46;
      }

      .paycan-theme-dark .paycan-badge-success {
        background: #064e3b;
        color: #6ee7b7;
      }

      .paycan-badge-warning {
        background: #fef3c7;
        color: #92400e;
      }

      .paycan-theme-dark .paycan-badge-warning {
        background: #78350f;
        color: #fcd34d;
      }

      .paycan-badge-error {
        background: #fee2e2;
        color: #991b1b;
      }

      .paycan-theme-dark .paycan-badge-error {
        background: #7f1d1d;
        color: #fca5a5;
      }

      .paycan-badge-info {
        background: #dbeafe;
        color: #1e40af;
      }

      .paycan-theme-dark .paycan-badge-info {
        background: #1e3a8a;
        color: #93c5fd;
      }

      /* Transactions List */
      .paycan-transactions-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
      }

      .paycan-transaction-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid;
      }

      .paycan-theme-light .paycan-transaction-row {
        background: #ffffff;
        border-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-transaction-row {
        background: #1f2937;
        border-color: #4b5563;
      }

      .paycan-transaction-info {
        flex: 1;
        min-width: 0;
      }

      .paycan-transaction-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.5rem;
      }

      .paycan-transaction-number {
        font-size: 0.875rem;
        font-weight: 500;
      }

      .paycan-transaction-meta {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.75rem;
      }

      .paycan-theme-light .paycan-transaction-meta {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-transaction-meta {
        color: #9ca3af;
      }

      .paycan-transaction-description {
        font-size: 0.875rem;
        margin: 0.5rem 0 0 0;
      }

      .paycan-theme-light .paycan-transaction-description {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-transaction-description {
        color: #9ca3af;
      }

      .paycan-transaction-amount {
        font-size: 1rem;
        font-weight: 500;
        flex-shrink: 0;
      }

      /* Buttons */
      .paycan-btn {
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 400;
        cursor: pointer;
        border: 1px solid transparent;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
      }

      .paycan-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
      }

      .paycan-btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.8125rem;
      }

      .paycan-btn-primary {
        background: var(--paycan-accent);
        color: white;
      }

      .paycan-btn-primary:hover:not(:disabled) {
        background: var(--paycan-accent-hover);
      }

      .paycan-btn-secondary {
        border-color: #d1d5db;
      }

      .paycan-theme-light .paycan-btn-secondary {
        background: #ffffff;
        color: #374151;
      }

      .paycan-theme-dark .paycan-btn-secondary {
        background: #374151;
        color: #f9fafb;
        border-color: #4b5563;
      }

      .paycan-theme-light .paycan-btn-secondary:hover:not(:disabled) {
        background: #f9fafb;
      }

      .paycan-theme-dark .paycan-btn-secondary:hover:not(:disabled) {
        background: #4b5563;
      }

      .paycan-btn-danger {
        background: #ef4444;
        color: white;
      }

      .paycan-btn-danger:hover:not(:disabled) {
        background: #dc2626;
      }

      /* Empty State */
      .paycan-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem 1rem;
        text-align: center;
      }

      .paycan-empty-state svg {
        margin-bottom: 1rem;
      }

      .paycan-theme-light .paycan-empty-state svg {
        color: #9ca3af;
      }

      .paycan-theme-dark .paycan-empty-state svg {
        color: #6b7280;
      }

      .paycan-empty-state p {
        font-size: 0.875rem;
        margin: 0;
      }

      .paycan-theme-light .paycan-empty-state p {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-empty-state p {
        color: #9ca3af;
      }

      /* Loading State */
      .paycan-loading-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem 1rem;
      }

      .paycan-spinner {
        width: 40px;
        height: 40px;
        border: 3px solid;
        border-radius: 50%;
        border-top-color: var(--paycan-accent);
        animation: paycan-spin 0.6s linear infinite;
      }

      .paycan-theme-light .paycan-spinner {
        border-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-spinner {
        border-color: #374151;
      }

      @keyframes paycan-spin {
        to { transform: rotate(360deg); }
      }

      .paycan-loading-state p {
        margin-top: 1rem;
        font-size: 0.875rem;
      }

      .paycan-theme-light .paycan-loading-state p {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-loading-state p {
        color: #9ca3af;
      }

      /* Pagination */
      .paycan-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
      }

      .paycan-page-info {
        font-size: 0.875rem;
      }

      .paycan-theme-light .paycan-page-info {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-page-info {
        color: #9ca3af;
      }

      /* Toast Notification */
      .paycan-toast {
        position: absolute;
        top: 5rem;
        left: 50%;
        transform: translateX(-50%) translateY(-100px);
        padding: 0.75rem 1rem;
        border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        opacity: 0;
        transition: all 0.3s;
        pointer-events: none;
        z-index: 1000;
        max-width: 90%;
      }

      .paycan-toast.show {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
      }

      .paycan-toast-content {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
      }

      .paycan-toast-success {
        background: #10b981;
        color: white;
      }

      .paycan-toast-error {
        background: #ef4444;
        color: white;
      }

      .paycan-toast-info {
        background: #3b82f6;
        color: white;
      }

      /* Responsive */
      @media (max-width: 640px) {
        .paycan-modal {
          max-width: 100%;
        }

        .paycan-info-grid {
          grid-template-columns: 1fr;
        }

        .paycan-transaction-row {
          flex-direction: column;
          align-items: flex-start;
        }

        .paycan-transaction-amount {
          align-self: flex-end;
        }
      }
    `;
    }
}

/**
 * PayCan Transactions List Modal Web Component
 *
 * A framework-agnostic modal to display user transactions
 */
class TransactionsModal {
    constructor(sdk, options = {}) {
        this.container = null;
        this.shadowRoot = null;
        this.overlay = null;
        this.modal = null;
        this.transactions = [];
        this.loading = false;
        this.currentPage = 1;
        this.totalPages = 1;
        /**
         * Handle escape key press
         */
        this.handleEscapeKey = (e) => {
            if (e.key === 'Escape') {
                this.close();
            }
        };
        this.sdk = sdk;
        this.options = options;
    }
    /**
     * Open the modal and load transactions
     */
    async open() {
        try {
            await this.loadTransactions();
            this.createModal();
        }
        catch (error) {
            this.handleError(error);
        }
    }
    /**
     * Close and destroy the modal
     */
    close() {
        if (this.container) {
            this.container.remove();
            this.container = null;
            this.shadowRoot = null;
            this.overlay = null;
            this.modal = null;
        }
        document.removeEventListener('keydown', this.handleEscapeKey);
        if (this.options.onClose) {
            this.options.onClose();
        }
    }
    /**
     * Load transactions from API
     */
    async loadTransactions(page = 1) {
        this.loading = true;
        try {
            const response = await this.sdk.get(`/api/user/transactions?page=${page}&per_page=20&include=order`);
            this.transactions = response.data;
            this.currentPage = response.meta?.current_page || 1;
            this.totalPages = response.meta?.last_page || 1;
            this.loading = false;
        }
        catch (error) {
            this.loading = false;
            throw error;
        }
    }
    /**
     * Create modal DOM structure with Shadow DOM
     */
    createModal() {
        const isDark = this.isDarkMode();
        const themeClass = isDark ? 'paycan-theme-dark' : 'paycan-theme-light';
        // Create container element for Shadow DOM
        this.container = document.createElement('div');
        this.container.setAttribute('id', 'paycan-transactions-modal');
        // Attach Shadow DOM
        this.shadowRoot = this.container.attachShadow({ mode: 'open' });
        // Create style element inside shadow root
        const styleEl = document.createElement('style');
        styleEl.textContent = this.getStyles();
        this.shadowRoot.appendChild(styleEl);
        // Create overlay inside shadow root
        this.overlay = document.createElement('div');
        this.overlay.className = `paycan-modal-overlay ${themeClass}`;
        // Create modal container
        this.modal = document.createElement('div');
        this.modal.className = `paycan-modal paycan-modal-wide ${themeClass}`;
        this.modal.innerHTML = this.getModalContent();
        this.overlay.appendChild(this.modal);
        this.shadowRoot.appendChild(this.overlay);
        // Append container to body
        document.body.appendChild(this.container);
        // Add event listeners
        this.attachEventListeners();
        // Close on overlay click
        this.overlay.addEventListener('click', (e) => {
            if (e.target === this.overlay) {
                this.close();
            }
        });
        // Close on Escape key
        document.addEventListener('keydown', this.handleEscapeKey);
    }
    /**
     * Refresh modal content
     */
    refreshModal() {
        if (this.modal) {
            this.modal.innerHTML = this.getModalContent();
            this.attachEventListeners();
        }
    }
    /**
     * Detect if dark mode should be used
     */
    isDarkMode() {
        const theme = this.options.theme || 'auto';
        if (theme === 'dark') {
            return true;
        }
        else if (theme === 'light') {
            return false;
        }
        else {
            return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        }
    }
    /**
     * Get modal HTML content
     */
    getModalContent() {
        return `
      <div class="paycan-modal-header">
        <h2 class="paycan-modal-title">Transaction History</h2>
        <button class="paycan-close-btn" aria-label="Close">×</button>
      </div>
      <div class="paycan-toast">
        <div class="paycan-toast-content"></div>
      </div>
      <div class="paycan-modal-body">
        ${this.loading ? this.getLoadingState() : this.getTransactionsList()}
      </div>
      ${this.getPagination()}
    `;
    }
    /**
     * Get transactions list HTML
     */
    getTransactionsList() {
        if (this.transactions.length === 0) {
            return `
        <div class="paycan-empty-state">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <p>No transactions found</p>
        </div>
      `;
        }
        return `
      <div class="paycan-transactions-list">
        ${this.transactions.map(tx => this.getTransactionRow(tx)).join('')}
      </div>
    `;
    }
    /**
     * Get transaction row HTML
     */
    getTransactionRow(tx) {
        const statusClass = this.getStatusClass(tx.status);
        const statusText = this.formatStatus(tx.status);
        return `
      <div class="paycan-transaction-row">
        <div class="paycan-transaction-info">
          <div class="paycan-transaction-header">
            <span class="paycan-transaction-number">${this.escapeHtml(tx.transaction_number)}</span>
            <span class="paycan-badge paycan-badge-${statusClass}">${statusText}</span>
          </div>
          <div class="paycan-transaction-meta">
            <span>${this.formatDate(tx.created_at)}</span>
            <span>•</span>
            <span>${this.formatGateway(tx.gateway)}</span>
            <span>•</span>
            <span>${this.formatTransactionType(tx.type)}</span>
            ${tx.order ? `
              <span>•</span>
              <span>Order: ${this.escapeHtml(tx.order.order_number)}</span>
            ` : ''}
          </div>
          ${tx.description ? `<p class="paycan-transaction-description">${this.escapeHtml(tx.description)}</p>` : ''}
        </div>
        <div class="paycan-transaction-amount">
          ${this.formatPrice(tx.amount, tx.currency)}
        </div>
      </div>
    `;
    }
    /**
     * Get loading state HTML
     */
    getLoadingState() {
        return `
      <div class="paycan-loading-state">
        <div class="paycan-spinner"></div>
        <p>Loading...</p>
      </div>
    `;
    }
    /**
     * Get pagination HTML
     */
    getPagination() {
        if (this.totalPages <= 1) {
            return '';
        }
        return `
      <div class="paycan-modal-footer">
        <div class="paycan-pagination">
          <button class="paycan-btn paycan-btn-secondary paycan-btn-sm" data-action="prev-page" ${this.currentPage === 1 ? 'disabled' : ''}>
            Previous
          </button>
          <span class="paycan-page-info">Page ${this.currentPage} of ${this.totalPages}</span>
          <button class="paycan-btn paycan-btn-secondary paycan-btn-sm" data-action="next-page" ${this.currentPage === this.totalPages ? 'disabled' : ''}>
            Next
          </button>
        </div>
      </div>
    `;
    }
    /**
     * Attach event listeners
     */
    attachEventListeners() {
        if (!this.modal)
            return;
        // Close button
        const closeBtn = this.modal.querySelector('.paycan-close-btn');
        closeBtn?.addEventListener('click', () => this.close());
        // Action buttons
        const actionButtons = this.modal.querySelectorAll('[data-action]');
        actionButtons.forEach(btn => {
            btn.addEventListener('click', async (e) => {
                const target = e.currentTarget;
                const action = target.dataset.action;
                if (!action)
                    return;
                try {
                    switch (action) {
                        case 'prev-page':
                            if (this.currentPage > 1) {
                                await this.loadTransactions(this.currentPage - 1);
                                this.refreshModal();
                            }
                            break;
                        case 'next-page':
                            if (this.currentPage < this.totalPages) {
                                await this.loadTransactions(this.currentPage + 1);
                                this.refreshModal();
                            }
                            break;
                    }
                }
                catch (error) {
                    this.handleError(error);
                }
            });
        });
    }
    /**
     * Show toast notification
     */
    showToast(message, type = 'info') {
        ToastHelper.showToast(this.modal, message, type);
    }
    /**
     * Handle errors
     */
    handleError(error) {
        this.showToast(error.message, 'error');
        if (this.options.onError) {
            this.options.onError(error);
        }
    }
    /**
     * Format price
     */
    formatPrice(amount, currency) {
        if (!currency) {
            return amount.toFixed(2);
        }
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: currency.toUpperCase(),
        }).format(amount);
    }
    /**
     * Format date
     */
    formatDate(date) {
        return new Date(date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    }
    /**
     * Format status
     */
    formatStatus(status) {
        return status.charAt(0).toUpperCase() + status.slice(1).replace(/_/g, ' ');
    }
    /**
     * Get status class
     */
    getStatusClass(status) {
        const statusMap = {
            'completed': 'success',
            'succeeded': 'success',
            'pending': 'warning',
            'processing': 'warning',
            'failed': 'error',
            'refunded': 'info',
        };
        return statusMap[status] || 'default';
    }
    /**
     * Format gateway name
     */
    formatGateway(gateway) {
        const gatewayMap = {
            'stripe': 'Stripe',
            'paypal': 'PayPal',
        };
        return gatewayMap[gateway] || gateway;
    }
    /**
     * Format transaction type
     */
    formatTransactionType(type) {
        const typeMap = {
            'payment': 'Payment',
            'refund': 'Refund',
            'subscription': 'Subscription',
        };
        return typeMap[type] || type;
    }
    /**
     * Escape HTML to prevent XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    /**
     * Get all modal CSS styles
     */
    getStyles() {
        return `
      /* PayCan Transactions Modal Styles */

      /* CSS Variables for Customization */
      .paycan-modal-overlay {
        --paycan-accent: #3b82f6;
        --paycan-accent-hover: #2563eb;
        --paycan-accent-light: #eff6ff;
        --paycan-accent-dark: #1e3a8a;
      }

      /* Overlay */
      .paycan-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 999999;
        padding: 1rem;
      }

      /* Modal Container */
      .paycan-modal {
        width: 100%;
        max-width: 600px;
        max-height: 90vh;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
      }

      .paycan-modal-wide {
        max-width: 800px;
      }

      /* Theme Colors */
      .paycan-modal.paycan-theme-light {
        background: #f9fafb;
        color: #111827;
      }

      .paycan-modal.paycan-theme-dark {
        background: #111827;
        color: #f9fafb;
      }

      /* Modal Header */
      .paycan-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid;
        flex-shrink: 0;
      }

      .paycan-theme-light .paycan-modal-header {
        border-bottom-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-modal-header {
        border-bottom-color: #374151;
      }

      .paycan-modal-title {
        font-size: 1.25rem;
        font-weight: 500;
        margin: 0;
      }

      /* Close Button */
      .paycan-close-btn {
        background: none;
        border: none;
        font-size: 1.5rem;
        font-weight: 200;
        cursor: pointer;
        padding: 0;
        line-height: 1;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: background 0.15s;
        box-shadow: none;
      }

      .paycan-theme-light .paycan-close-btn {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-close-btn {
        color: #9ca3af;
      }

      .paycan-theme-light .paycan-close-btn:hover {
        background: #f3f4f6;
      }

      .paycan-theme-dark .paycan-close-btn:hover {
        background: #374151;
      }

      /* Modal Body */
      .paycan-modal-body {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
      }

      /* Modal Footer */
      .paycan-modal-footer {
        padding: 1rem 1.25rem;
        border-top: 1px solid;
        flex-shrink: 0;
      }

      .paycan-theme-light .paycan-modal-footer {
        border-top-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-modal-footer {
        border-top-color: #374151;
      }

      /* Badge */
      .paycan-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-weight: 500;
        flex-shrink: 0;
      }

      .paycan-badge-success {
        background: #d1fae5;
        color: #065f46;
      }

      .paycan-theme-dark .paycan-badge-success {
        background: #064e3b;
        color: #6ee7b7;
      }

      .paycan-badge-warning {
        background: #fef3c7;
        color: #92400e;
      }

      .paycan-theme-dark .paycan-badge-warning {
        background: #78350f;
        color: #fcd34d;
      }

      .paycan-badge-error {
        background: #fee2e2;
        color: #991b1b;
      }

      .paycan-theme-dark .paycan-badge-error {
        background: #7f1d1d;
        color: #fca5a5;
      }

      .paycan-badge-info {
        background: #dbeafe;
        color: #1e40af;
      }

      .paycan-theme-dark .paycan-badge-info {
        background: #1e3a8a;
        color: #93c5fd;
      }

      /* Transactions List */
      .paycan-transactions-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
      }

      .paycan-transaction-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid;
      }

      .paycan-theme-light .paycan-transaction-row {
        background: #ffffff;
        border-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-transaction-row {
        background: #1f2937;
        border-color: #4b5563;
      }

      .paycan-transaction-info {
        flex: 1;
        min-width: 0;
      }

      .paycan-transaction-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.5rem;
      }

      .paycan-transaction-number {
        font-size: 0.875rem;
        font-weight: 500;
      }

      .paycan-transaction-meta {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.75rem;
        flex-wrap: wrap;
      }

      .paycan-theme-light .paycan-transaction-meta {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-transaction-meta {
        color: #9ca3af;
      }

      .paycan-transaction-description {
        font-size: 0.875rem;
        margin: 0.5rem 0 0 0;
      }

      .paycan-theme-light .paycan-transaction-description {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-transaction-description {
        color: #9ca3af;
      }

      .paycan-transaction-amount {
        font-size: 1rem;
        font-weight: 500;
        flex-shrink: 0;
      }

      /* Buttons */
      .paycan-btn {
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 400;
        cursor: pointer;
        border: 1px solid transparent;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
      }

      .paycan-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
      }

      .paycan-btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.8125rem;
      }

      .paycan-btn-secondary {
        border-color: #d1d5db;
      }

      .paycan-theme-light .paycan-btn-secondary {
        background: #ffffff;
        color: #374151;
      }

      .paycan-theme-dark .paycan-btn-secondary {
        background: #374151;
        color: #f9fafb;
        border-color: #4b5563;
      }

      .paycan-theme-light .paycan-btn-secondary:hover:not(:disabled) {
        background: #f9fafb;
      }

      .paycan-theme-dark .paycan-btn-secondary:hover:not(:disabled) {
        background: #4b5563;
      }

      /* Empty State */
      .paycan-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem 1rem;
        text-align: center;
      }

      .paycan-empty-state svg {
        margin-bottom: 1rem;
      }

      .paycan-theme-light .paycan-empty-state svg {
        color: #9ca3af;
      }

      .paycan-theme-dark .paycan-empty-state svg {
        color: #6b7280;
      }

      .paycan-empty-state p {
        font-size: 0.875rem;
        margin: 0;
      }

      .paycan-theme-light .paycan-empty-state p {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-empty-state p {
        color: #9ca3af;
      }

      /* Loading State */
      .paycan-loading-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem 1rem;
      }

      .paycan-spinner {
        width: 40px;
        height: 40px;
        border: 3px solid;
        border-radius: 50%;
        border-top-color: var(--paycan-accent);
        animation: paycan-spin 0.6s linear infinite;
      }

      .paycan-theme-light .paycan-spinner {
        border-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-spinner {
        border-color: #374151;
      }

      @keyframes paycan-spin {
        to { transform: rotate(360deg); }
      }

      .paycan-loading-state p {
        margin-top: 1rem;
        font-size: 0.875rem;
      }

      .paycan-theme-light .paycan-loading-state p {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-loading-state p {
        color: #9ca3af;
      }

      /* Pagination */
      .paycan-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
      }

      .paycan-page-info {
        font-size: 0.875rem;
      }

      .paycan-theme-light .paycan-page-info {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-page-info {
        color: #9ca3af;
      }

      /* Toast Notification */
      .paycan-toast {
        position: absolute;
        top: 5rem;
        left: 50%;
        transform: translateX(-50%) translateY(-100px);
        padding: 0.75rem 1rem;
        border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        opacity: 0;
        transition: all 0.3s;
        pointer-events: none;
        z-index: 1000;
        max-width: 90%;
      }

      .paycan-toast.show {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
      }

      .paycan-toast-content {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
      }

      .paycan-toast-success {
        background: #10b981;
        color: white;
      }

      .paycan-toast-error {
        background: #ef4444;
        color: white;
      }

      .paycan-toast-info {
        background: #3b82f6;
        color: white;
      }

      /* Responsive */
      @media (max-width: 640px) {
        .paycan-modal {
          max-width: 100%;
        }

        .paycan-transaction-row {
          flex-direction: column;
          align-items: flex-start;
        }

        .paycan-transaction-amount {
          align-self: flex-end;
        }
      }
    `;
    }
}

var transactionsModal = /*#__PURE__*/Object.freeze({
    __proto__: null,
    TransactionsModal: TransactionsModal
});

exports.CheckoutModal = CheckoutModal;
exports.OrdersModal = OrdersModal;
exports.PayCan = PayCan;
exports.ProductsModal = ProductsModal;
exports.SubscriptionsModal = SubscriptionsModal;
exports.TransactionsModal = TransactionsModal;
exports.default = PayCan;
//# sourceMappingURL=index.js.map
