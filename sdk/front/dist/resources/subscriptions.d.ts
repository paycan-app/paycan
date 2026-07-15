/**
 * Subscriptions Resource
 *
 * Handle subscription-related operations
 */
import type { HttpClient } from '../http-client';
import type { Subscription, PaginatedResponse, PayCanConfig } from '../types';
export declare class Subscriptions {
    private http;
    private subscriptionsCache;
    private cacheTimestamp;
    private readonly cacheTtl;
    private readonly CACHE_KEY;
    private readonly CACHE_TS_KEY;
    constructor(http: HttpClient, config: PayCanConfig);
    /**
     * Load cache from localStorage
     */
    private loadCacheFromStorage;
    /**
     * Save cache to localStorage
     */
    private saveCacheToStorage;
    /**
     * Clear the subscriptions cache
     * Call this after subscription changes (cancel, resume, upgrade)
     */
    clearCache(): void;
    /**
     * Get cached subscriptions or fetch fresh data
     * PERFORMANCE TIP: This method caches subscriptions to avoid rate limits
     * and improve performance when checking multiple products/prices.
     * Cache can be disabled by setting cacheTtl to 0.
     */
    private getCachedSubscriptions;
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
    list(params?: {
        filter?: {
            status?: string;
            gateway?: string;
            created_after?: string;
            created_before?: string;
        };
        include?: string;
        sort?: string;
        per_page?: number;
        page?: number;
    }): Promise<PaginatedResponse<Subscription>>;
    /**
     * List all active subscriptions (uses cache)
     *
     * @example
     * const activeSubscriptions = await paycan.subscriptions.listActive();
     */
    listActive(): Promise<Subscription[]>;
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
    get(subscriptionId: number | string, params?: {
        include?: string;
    }): Promise<{
        data: Subscription;
    }>;
    /**
     * Cancel a subscription
     *
     * @example
     * await paycan.subscriptions.cancel(123);
     */
    cancel(subscriptionId: number | string): Promise<{
        subscription: Subscription;
    }>;
    /**
     * Resume a cancelled subscription
     *
     * @example
     * await paycan.subscriptions.resume(123);
     */
    resume(subscriptionId: number | string): Promise<{
        subscription: Subscription;
    }>;
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
    change(subscriptionId: number | string, data: {
        product_price_id: string;
        prorate?: boolean;
    }): Promise<{
        subscription: Subscription;
        message: string;
    }>;
}
//# sourceMappingURL=subscriptions.d.ts.map