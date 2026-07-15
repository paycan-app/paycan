/**
 * Products Resource
 *
 * Handle product-related operations
 */
import type { HttpClient } from '../http-client';
import type { Product, PaginatedResponse } from '../types';
export declare class Products {
    private http;
    constructor(http: HttpClient);
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
    list(params?: {
        filter?: {
            type?: 'physical' | 'digital' | 'service' | 'subscription';
        };
        include?: string;
        sort?: string;
        per_page?: number;
        page?: number;
    }): Promise<PaginatedResponse<Product>>;
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
    get(productId: string, params?: {
        include?: string;
    }): Promise<{
        data: Product;
    }>;
}
//# sourceMappingURL=products.d.ts.map