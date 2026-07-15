/**
 * Subscriptions Resource
 *
 * Handle subscription-related operations
 */

import type { HttpClient } from '../http-client';
import type { Subscription, PaginatedResponse, PayCanConfig } from '../types';

export class Subscriptions {
  private subscriptionsCache: Subscription[] | null = null;
  private cacheTimestamp: number | null = null;
  private readonly cacheTtl: number; // Cache TTL in milliseconds
  private readonly CACHE_KEY = 'paycan_subscriptions_cache';
  private readonly CACHE_TS_KEY = 'paycan_subscriptions_cache_timestamp';

  constructor(private http: HttpClient, config: PayCanConfig) {
    // Get cache TTL from config (default 60 seconds), convert to milliseconds
    const cacheTTLSeconds = config.cacheTtl ?? 60;
    this.cacheTtl = cacheTTLSeconds * 1000;

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
        this.subscriptionsCache = JSON.parse(cached);
        this.cacheTimestamp = parseInt(timestamp, 10);
      }
    } catch (error) {
      // Silently fail if localStorage is unavailable or data is corrupted
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
      if (this.subscriptionsCache && this.cacheTimestamp) {
        localStorage.setItem(this.CACHE_KEY, JSON.stringify(this.subscriptionsCache));
        localStorage.setItem(this.CACHE_TS_KEY, this.cacheTimestamp.toString());
      }
    } catch (error) {
      // Silently fail if localStorage quota exceeded or unavailable
    }
  }

  /**
   * Clear the subscriptions cache
   * Call this after subscription changes (cancel, resume, upgrade)
   */
  clearCache(): void {
    this.subscriptionsCache = null;
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
   * Get cached subscriptions or fetch fresh data
   * PERFORMANCE TIP: This method caches subscriptions to avoid rate limits
   * and improve performance when checking multiple products/prices.
   * Cache can be disabled by setting cacheTtl to 0.
   */
  private async getCachedSubscriptions(): Promise<Subscription[]> {
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
      return this.subscriptionsCache!;
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
  async list(params?: {
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
  }): Promise<PaginatedResponse<Subscription>> {
    // Clear cache when explicitly listing to ensure fresh data
    this.clearCache();
    return this.http.get<PaginatedResponse<Subscription>>('/api/user/subscriptions', params);
  }

  /**
   * List all active subscriptions (uses cache)
   *
   * @example
   * const activeSubscriptions = await paycan.subscriptions.listActive();
   */
  async listActive(): Promise<Subscription[]> {
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
  async get(subscriptionId: number | string, params?: { include?: string }): Promise<{ data: Subscription }> {
    // Check cache first (only if no specific includes requested)
    if (!params?.include) {
      const cachedSubscriptions = await this.getCachedSubscriptions();
      const cached = cachedSubscriptions.find((sub) => sub.id === Number(subscriptionId));

      if (cached) {
        return { data: cached };
      }
    }

    // Not in cache or specific includes requested, fetch from API
    return this.http.get<{ data: Subscription }>(
      `/api/user/subscriptions/${subscriptionId}`,
      params
    );
  }

  /**
   * Cancel a subscription
   *
   * @example
   * await paycan.subscriptions.cancel(123);
   */
  async cancel(subscriptionId: number | string): Promise<{ subscription: Subscription }> {
    const result = await this.http.post<{ subscription: Subscription }>(
      `/api/user/subscriptions/${subscriptionId}/cancel`
    );
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
  async resume(subscriptionId: number | string): Promise<{ subscription: Subscription }> {
    const result = await this.http.post<{ subscription: Subscription }>(
      `/api/user/subscriptions/${subscriptionId}/resume`
    );
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
  async change(
    subscriptionId: number | string,
    data: { product_price_id: string; prorate?: boolean }
  ): Promise<{ subscription: Subscription; message: string }> {
    const result = await this.http.post<{ subscription: Subscription; message: string }>(
      `/api/user/subscriptions/${subscriptionId}/change`,
      data
    );
    // Clear cache after mutation
    this.clearCache();
    return result;
  }

}
