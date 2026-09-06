/**
 * Wallets Resource
 *
 * Handle wallet and credit-related operations
 */

import type { HttpClient } from '../http-client';
import type {
  Wallet,
  WalletTransaction,
  DeductCreditsParams,
  TopupCreditsParams,
  WalletTransactionsParams,
  WalletUsagesParams,
  PaginatedResponse,
} from '../types';

export class Wallets {
  constructor(private http: HttpClient) {}

  /**
   * List all wallets for the authenticated user
   *
   * @example
   * const { data: wallets } = await paycan.wallets.list();
   * console.log(wallets); // [{ type: 'basic', balance: 1000 }, { type: 'premium', balance: 50 }]
   */
  async list(): Promise<{ success: boolean; data: Wallet[] }> {
    return this.http.get<{ success: boolean; data: Wallet[] }>('/api/user/wallets');
  }

  /**
   * Get a specific wallet by type
   *
   * @param type - Wallet type ('basic', 'premium', or custom)
   *
   * @example
   * const { data: basicWallet } = await paycan.wallets.get('basic');
   */
  async get(type: string = 'basic'): Promise<{ success: boolean; data: Wallet }> {
    return this.http.get<{ success: boolean; data: Wallet }>(`/api/user/wallets/${type}`);
  }

  /**
   * Get wallet transactions for the authenticated user
   *
   * Can be called with:
   * - No arguments: returns all transactions across all wallets
   * - A wallet type string (e.g. 'basic', 'premium')
   * - An options object with pagination and filtering params
   *
   * @example
   * // All transactions
   * const allTxs = await paycan.wallets.transactions();
   *
   * // Transactions for basic wallet
   * const basicTxs = await paycan.wallets.transactions('basic', { per_page: 20 });
   *
   * // With filter object
   * const creditTxs = await paycan.wallets.transactions({
   *   filter: { action: 'subscription_grant' },
   *   per_page: 10
   * });
   */
  async transactions(
    typeOrParams?: string | WalletTransactionsParams,
    maybeParams?: WalletTransactionsParams
  ): Promise<PaginatedResponse<WalletTransaction>> {
    if (typeof typeOrParams === 'string') {
      return this.http.get<PaginatedResponse<WalletTransaction>>(
        `/api/user/wallets/${typeOrParams}/transactions`,
        maybeParams
      );
    }

    const params = typeOrParams || {};
    return this.http.get<PaginatedResponse<WalletTransaction>>('/api/user/wallets/transactions', params);
  }

  /**
   * Get credit usage logs for the authenticated user
   *
   * @param params - Query parameters for pagination and filtering
   *
   * @example
   * const usages = await paycan.wallets.usages({
   *   filter: { wallet_type: 'premium' },
   *   per_page: 15
   * });
   */
  async usages(params?: WalletUsagesParams): Promise<PaginatedResponse<WalletTransaction>> {
    return this.http.get<PaginatedResponse<WalletTransaction>>('/api/user/wallets/usages', params);
  }

  /**
   * Get credit usage logs (alias for usages)
   */
  async listUsages(params?: WalletUsagesParams): Promise<PaginatedResponse<WalletTransaction>> {
    return this.usages(params);
  }

  /**
   * List all wallet transactions across all wallets (alias for transactions())
   */
  async listAllTransactions(params?: WalletTransactionsParams): Promise<PaginatedResponse<WalletTransaction>> {
    return this.transactions(params);
  }

  /**
   * List transactions for a specific wallet type
   */
  async listTransactions(
    type: string,
    params?: WalletTransactionsParams
  ): Promise<PaginatedResponse<WalletTransaction>> {
    return this.transactions(type, params);
  }

  /**
   * Get all wallet transactions for a specific customer (Admin / Backend)
   *
   * @param userId - Target user / customer ID
   * @param params - Optional query parameters
   */
  async getUserTransactions(
    userId: string,
    params?: WalletTransactionsParams
  ): Promise<PaginatedResponse<WalletTransaction>> {
    return this.http.get<PaginatedResponse<WalletTransaction>>(
      `/api/admin/users/${userId}/wallets/transactions`,
      params
    );
  }

  /**
   * Get usage logs for a specific customer (Admin / Backend)
   *
   * @param userId - Target user / customer ID
   * @param params - Optional query parameters
   */
  async getUserUsages(
    userId: string,
    params?: WalletUsagesParams
  ): Promise<PaginatedResponse<WalletTransaction>> {
    return this.http.get<PaginatedResponse<WalletTransaction>>(
      `/api/admin/users/${userId}/wallets/usages`,
      params
    );
  }

  /**
   * Top up credits for a user's wallet (Admin / Backend)
   *
   * @param params - Top up parameters
   *
   * @example
   * await paycan.wallets.topup({
   *   user_id: 'usr_123',
   *   wallet_type: 'basic',
   *   amount: 500,
   *   description: 'Promotional gift'
   * });
   */
  async topup(params: TopupCreditsParams): Promise<{
    success: boolean;
    message: string;
    data: {
      transaction: WalletTransaction;
      wallet: {
        type: string;
        balance: number;
      };
    };
  }> {
    return this.http.post('/api/admin/wallets/topup', params);
  }

  /**
   * Deduct credits from user's wallet (e.g. for AI agent usage)
   *
   * If `params.user_id` is provided, calls the admin endpoint.
   * Otherwise, calls the authenticated user endpoint.
   *
   * @param params - Deduction parameters
   *
   * @example
   * const result = await paycan.wallets.deduct({
   *   wallet_type: 'premium',
   *   amount: 2.5,
   *   description: 'Agent generation task #12',
   *   reference_id: 'task_12'
   * });
   */
  async deduct(params: DeductCreditsParams): Promise<{
    success: boolean;
    message: string;
    data: {
      transaction: WalletTransaction;
      wallet: {
        type: string;
        balance: number;
      };
    };
  }> {
    const endpoint = params.user_id ? '/api/admin/wallets/deduct' : '/api/user/wallets/deduct';
    return this.http.post(endpoint, params);
  }
}

