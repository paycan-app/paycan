/**
 * Orders Resource
 *
 * Handle order-related operations
 */
import type { HttpClient } from '../http-client';
import type { Order, PaginatedResponse, DownloadLink, LicenseKey, PayCanConfig } from '../types';
export declare class Orders {
    private http;
    private ordersCache;
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
     * Clear the orders cache
     *
     * Note: Cache is automatically cleared when creating new orders or completing checkouts
     * to ensure fresh data is fetched on next request.
     */
    clearCache(): void;
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
    list(params?: {
        filter?: {
            status?: string;
            gateway?: string;
            order_number?: string;
            created_after?: string;
            created_before?: string;
        };
        include?: string;
        sort?: string;
        per_page?: number;
        page?: number;
    }): Promise<PaginatedResponse<Order>>;
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
    get(orderId: string, params?: {
        include?: string;
    }): Promise<{
        data: Order;
    }>;
    /**
     * Get download links for an order
     *
     * @example
     * const downloads = await paycan.orders.getDownloads('order-id-123');
     */
    getDownloads(orderId: string): Promise<{
        order_id: string;
        downloads: DownloadLink[];
    }>;
    /**
     * Get license keys for an order
     *
     * @example
     * const licenses = await paycan.orders.getLicenses('order-id-123');
     */
    getLicenses(orderId: string): Promise<{
        order_id: string;
        licenses: LicenseKey[];
    }>;
}
//# sourceMappingURL=orders.d.ts.map