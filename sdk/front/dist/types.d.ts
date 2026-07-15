/**
 * PayCan SDK Types
 *
 * Type definitions for the PayCan JavaScript SDK
 */
export interface PayCanConfig {
    /** Your PayCan instance URL (e.g., 'https://pay.yourapp.com') */
    apiUrl: string;
    /** Optional: Enable debug logging */
    debug?: boolean;
    /** Optional: Token refresh threshold in seconds (default: 300 = 5 min before expiry) */
    refreshThreshold?: number;
    /** Optional: Cache TTL in seconds for subscriptions and orders (default: 60). Set to 0 to disable caching. */
    cacheTtl?: number;
}
export type AuthenticationType = 'bearer' | 'cookie' | 'custom';
export interface AuthenticationConfig {
    /** Your backend endpoint that provides PayCan token */
    endpoint: string;
    /** Authentication type (default: 'bearer' for Authorization header) */
    type?: AuthenticationType;
    /** Optional: Custom headers to send with auth request */
    headers?: Record<string, string>;
    /** Optional: Additional data to send in auth request body */
    data?: Record<string, any>;
}
export interface AuthResponse {
    token: string;
    user?: User;
}
export interface User {
    id: string;
    name: string;
    email: string;
    [key: string]: any;
}
export interface Order {
    id: number;
    order_number: string;
    status: 'pending' | 'processing' | 'completed' | 'failed' | 'cancelled' | 'refunded';
    total: number;
    currency: string;
    product_price?: ProductPrice;
    transactions?: Transaction[];
    fulfillments?: Fulfillment[];
    created_at: string;
    updated_at: string;
    [key: string]: any;
}
export interface ProductPrice {
    id: string;
    title: string;
    amount: number;
    currency: string;
    billing_period: 'once' | 'daily' | 'weekly' | 'monthly' | 'yearly';
    product?: Product;
    [key: string]: any;
}
export interface Product {
    id: string;
    title: string;
    slug: string;
    type: 'physical' | 'digital' | 'service' | 'subscription';
    is_active: boolean;
    [key: string]: any;
}
export interface Transaction {
    id: number;
    type: 'charge' | 'refund';
    status: 'pending' | 'completed' | 'failed';
    amount: number;
    currency: string;
    gateway: string;
    gateway_transaction_id?: string;
    created_at: string;
    [key: string]: any;
}
export interface Fulfillment {
    id: string;
    status: 'pending' | 'processing' | 'completed' | 'failed';
    type: 'physical' | 'digital';
    meta?: {
        license_key?: string;
        download_link?: string;
        expires_at?: string;
        [key: string]: any;
    };
    fulfilled_at?: string;
    created_at: string;
    [key: string]: any;
}
export interface Subscription {
    id: number;
    status: 'active' | 'trialing' | 'past_due' | 'canceled' | 'incomplete' | 'incomplete_expired';
    product_price?: ProductPrice;
    current_period_start?: string;
    current_period_end?: string;
    next_billing_date?: string | null;
    ends_at?: string | null;
    canceled_at?: string | null;
    trial_ends_at?: string | null;
    can_resume?: boolean;
    created_at: string;
    updated_at: string;
    [key: string]: any;
}
export interface DownloadLink {
    product_id: string;
    product_title: string;
    download_url: string | null;
    expires_at: string | null;
}
export interface LicenseKey {
    product_id: string;
    product_title: string;
    license_key: string;
    expires_at: string | null;
}
export interface CheckoutSession {
    session_id: string;
    checkout_url: string;
    order_id: number;
    subscription_id?: number;
}
export interface PaginatedResponse<T> {
    data: T[];
    links?: {
        first: string | null;
        last: string | null;
        prev: string | null;
        next: string | null;
    };
    meta?: {
        current_page: number;
        from: number | null;
        last_page: number;
        per_page: number;
        to: number | null;
        total: number;
    };
}
export interface ApiError {
    message: string;
    errors?: Record<string, string[]>;
    status?: number;
}
export interface CreateOrderData {
    product_price_id: string;
    gateway?: string;
    customer_note?: string;
    [key: string]: any;
}
export interface CreateCheckoutData {
    product_id: string | number;
    product_price_id: string | number;
    gateway: 'stripe' | 'paypal';
    billing_email?: string;
    billing_name?: string;
    billing_country?: string;
    billing_state?: string;
    quantity?: number;
    shipping_address?: {
        line1?: string;
        line2?: string;
        city?: string;
        state?: string;
        postal_code?: string;
        country?: string;
    };
}
export interface CheckoutPreviewPrice {
    id: string | number;
    name: string;
    amount: number;
    currency: string;
    billing_period: 'once' | 'daily' | 'weekly' | 'monthly' | 'yearly';
    trial_days?: number | null;
    is_recurring: boolean;
    subtotal: number;
    final_price: number;
}
export interface CheckoutPreviewProduct {
    id: string | number;
    name: string;
    type: 'physical' | 'digital' | 'service' | 'subscription';
    description?: string | null;
    image?: string | null;
}
export interface CheckoutPreviewGateway {
    key: string;
    name: string;
    icon?: string | null;
    description?: string | null;
    supports_subscriptions: boolean;
}
export interface CheckoutPreviewResponse {
    product: CheckoutPreviewProduct;
    selected_price?: CheckoutPreviewPrice | null;
    prices: CheckoutPreviewPrice[];
    quantity: number;
    payment_methods: CheckoutPreviewGateway[];
}
export interface GetCheckoutPreviewParams {
    product_id?: string | number;
    product_price_id?: string | number;
    selected_price_id?: string | number;
    gateway?: 'stripe' | 'paypal';
    quantity?: number;
    billing_country?: string;
    billing_state?: string;
}
//# sourceMappingURL=types.d.ts.map