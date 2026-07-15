/**
 * Transactions Resource
 *
 * Handle transaction-related operations
 */

import type { HttpClient } from '../http-client';
import type { Transaction, PaginatedResponse } from '../types';

export class Transactions {
  constructor(private http: HttpClient) {}

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
  async list(params?: {
    filter?: {
      type?: 'charge' | 'refund';
      status?: 'pending' | 'completed' | 'failed';
      gateway?: string;
      created_after?: string;
      created_before?: string;
    };
    include?: string;
    sort?: string;
    per_page?: number;
    page?: number;
  }): Promise<PaginatedResponse<Transaction>> {
    return this.http.get<PaginatedResponse<Transaction>>('/api/user/transactions', params);
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
  async get(transactionId: string, params?: { include?: string }): Promise<{ data: Transaction }> {
    return this.http.get<{ data: Transaction }>(`/api/user/transactions/${transactionId}`, params);
  }
}
