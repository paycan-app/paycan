/**
 * PayCan SDK - Main Client
 *
 * Official JavaScript SDK for PayCan payment integration
 */
import { Orders } from './resources/orders';
import { Subscriptions } from './resources/subscriptions';
import { Checkout } from './resources/checkout';
import { Products } from './resources/products';
import { Transactions } from './resources/transactions';
import { CheckoutModal, type CheckoutModalOptions } from './components/checkout-modal';
import { ProductsModal, type ProductsModalOptions } from './components/products-modal';
import type { PayCanConfig, AuthResponse, AuthenticationConfig, User } from './types';
export declare class PayCan {
    private http;
    /** Orders API */
    orders: Orders;
    /** Subscriptions API */
    subscriptions: Subscriptions;
    /** Checkout API */
    checkout: Checkout;
    /** Products API */
    products: Products;
    /** Transactions API */
    transactions: Transactions;
    /**
     * Create a new PayCan instance
     *
     * @example
     * const paycan = new PayCan({
     *   apiUrl: 'https://pay.yourapp.com',
     *   debug: true
     * });
     */
    constructor(config: PayCanConfig);
    /**
     * Get current user information
     *
     * @example
     * const { user } = await paycan.me();
     */
    me(): Promise<{
        user: User;
    }>;
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
    setUserToken(token: string): void;
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
    authenticate(config: AuthenticationConfig): Promise<AuthResponse>;
    /**
     * Check if user is authenticated
     *
     * @example
     * if (paycan.isAuthenticated()) {
     *   // User is logged in
     * }
     */
    isAuthenticated(): boolean;
    /**
     * Get current token (useful for debugging)
     */
    getToken(): string | null;
    /**
     * Logout the user by clearing the authentication token
     *
     * @example
     * paycan.logout();
     */
    logout(): void;
    /**
     * Make a GET request to the API
     * Exposed for use by modal components
     */
    get<T = any>(url: string): Promise<T>;
    /**
     * Make a POST request to the API
     * Exposed for use by modal components
     */
    post<T = any>(url: string, data: any): Promise<T>;
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
    openCheckoutModal(productId: number | string, options?: Partial<CheckoutModalOptions>): CheckoutModal;
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
    openCheckoutModalPrice(priceId: number | string, options?: Partial<CheckoutModalOptions>): CheckoutModal;
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
    openProductsModal(options?: Partial<ProductsModalOptions>): ProductsModal;
    /**
     * Validate configuration
     */
    private validateConfig;
    /**
     * Debug logging
     */
    private log;
    refreshToken(): Promise<void>;
}
//# sourceMappingURL=paycan.d.ts.map