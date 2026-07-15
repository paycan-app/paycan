/**
 * Orders Resource
 *
 * Handle order-related operations
 */

import type { HttpClient } from '../http-client';
import type {
  Order,
  PaginatedResponse,
  DownloadLink,
  LicenseKey,
  PayCanConfig,
} from '../types';

export class Orders {
  private ordersCache: Order[] | null = null;
  private cacheTimestamp: number | null = null;
  private readonly cacheTtl: number;
  private readonly CACHE_KEY = 'paycan_orders_cache';
  private readonly CACHE_TS_KEY = 'paycan_orders_cache_timestamp';

  constructor(private http: HttpClient, config: PayCanConfig) {
    const cacheTTLSeconds = config.cacheTtl ?? 60;
    this.cacheTtl = cacheTTLSeconds * 1000; // Convert to milliseconds

    // Load cache from localStorage on initialization
    this.loadCacheFromStorage();
  }

  /**
   * Load cache from localStorage
   */
  private loadCacheFromStorage(): void {
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
        } else {
          this.clearCache();
        }
      }
    } catch (error) {
      this.clearCache();
    }
  }

  /**
   * Save cache to localStorage
   */
  private saveCacheToStorage(): void {
    if (typeof window === 'undefined' || typeof localStorage === 'undefined') {
      return;
    }

    try {
      if (this.ordersCache && this.cacheTimestamp) {
        localStorage.setItem(this.CACHE_KEY, JSON.stringify(this.ordersCache));
        localStorage.setItem(this.CACHE_TS_KEY, this.cacheTimestamp.toString());
      }
    } catch (error) {
      // Silently fail if localStorage quota exceeded or unavailable
    }
  }

  /**
   * Clear the orders cache
   *
   * Note: Cache is automatically cleared when creating new orders or completing checkouts
   * to ensure fresh data is fetched on next request.
   */
  clearCache(): void {
    this.ordersCache = null;
    this.cacheTimestamp = null;

    if (typeof window !== 'undefined' && typeof localStorage !== 'undefined') {
      try {
        localStorage.removeItem(this.CACHE_KEY);
        localStorage.removeItem(this.CACHE_TS_KEY);
      } catch (error) {
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
  async list(params?: {
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
  }): Promise<PaginatedResponse<Order>> {
    const result = await this.http.get<PaginatedResponse<Order>>('/api/user/orders', params);

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
  async get(orderId: string, params?: { include?: string }): Promise<{ data: Order }> {
    return this.http.get<{ data: Order }>(`/api/user/orders/${orderId}`, params);
  }

  /**
   * Get download links for an order
   *
   * @example
   * const downloads = await paycan.orders.getDownloads('order-id-123');
   */
  async getDownloads(orderId: string): Promise<{ order_id: string; downloads: DownloadLink[] }> {
    return this.http.get<{ order_id: string; downloads: DownloadLink[] }>(
      `/api/user/orders/${orderId}/downloads`
    );
  }

  /**
   * Get license keys for an order
   *
   * @example
   * const licenses = await paycan.orders.getLicenses('order-id-123');
   */
  async getLicenses(orderId: string): Promise<{ order_id: string; licenses: LicenseKey[] }> {
    return this.http.get<{ order_id: string; licenses: LicenseKey[] }>(
      `/api/user/orders/${orderId}/licenses`
    );
  }
}
