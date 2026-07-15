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

class CheckoutLite {
    constructor(http) {
        this.http = http;
    }
    async create(data) {
        // Always skip auth check for checkout endpoint
        // The endpoint supports both authenticated users (will use their token if valid)
        // and guest users (will create user based on billing_email)
        // This prevents 401 errors when a user has an expired token in localStorage
        const response = await this.http.post('/api/user/checkout', data, true // skipAuthCheck
        );
        return response.checkout;
    }
    async getPortalUrl(returnUrl) {
        const response = await this.http.post('/api/user/checkout/portal', returnUrl ? { return_url: returnUrl } : undefined);
        return response.portal;
    }
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
        // Public endpoint; skip auth check
        return this.http.get(`/api/user/checkout/preview?${queryParams.toString()}`, undefined, true);
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
 * PayCan SDK - API-Only Client
 *
 * Exposes core API helpers + Checkout + CheckoutModal
 * Excludes Orders, Subscriptions, Transactions, ProductsModal, and change-plan flows.
 */
class PayCanApi {
    constructor(config) {
        this.validateConfig(config);
        this.http = new HttpClient(config);
        this.checkout = new CheckoutLite(this.http);
    }
    async me() {
        return this.http.get('/api/user/me');
    }
    setUserToken(token) {
        if (!token) {
            throw new Error('Token is required');
        }
        this.http.setToken(token);
        this.log('User token set successfully');
    }
    async authenticate(config) {
        try {
            const headers = {
                'Content-Type': 'application/json',
                ...config.headers,
            };
            const fetchOptions = { method: 'POST', headers };
            if (config.type === 'cookie') {
                fetchOptions.credentials = 'include';
            }
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
    isAuthenticated() {
        return this.http.getToken() !== null;
    }
    getToken() {
        return this.http.getToken();
    }
    logout() {
        this.http.clearToken();
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
    async get(url) {
        return this.http.get(url);
    }
    async post(url, data) {
        return this.http.post(url, data);
    }
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
    validateConfig(config) {
        if (!config.apiUrl) {
            throw new Error('PayCan: apiUrl is required');
        }
        config.apiUrl = config.apiUrl.replace(/\/$/, '');
    }
    log(...args) {
        const config = this.http['config'];
        if (config?.debug) {
            console.log('[PayCan SDK]', ...args);
        }
    }
    async refreshToken() {
        await this.http.refreshToken();
    }
}

export { CheckoutModal, PayCanApi, PayCanApi as default };
//# sourceMappingURL=api.esm.js.map
