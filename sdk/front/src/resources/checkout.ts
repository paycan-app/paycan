/**
 * Checkout Resource
 *
 * Handle checkout and payment portal operations
 */

import type { HttpClient } from '../http-client';
import type { CheckoutSession, CreateCheckoutData, GetCheckoutPreviewParams, CheckoutPreviewResponse } from '../types';
import type { Orders } from './orders';
import type { Subscriptions } from './subscriptions';

export class Checkout {
  constructor(
    private http: HttpClient,
    private orders: Orders,
    private subscriptions: Subscriptions
  ) {}

  /**
   * Create a checkout session
   *
   * @example
   * const session = await paycan.checkout.create({
   *   product_id: 1,
   *   product_price_id: 1,
   *   gateway: 'stripe',
   *   billing_email: 'user@example.com', // Optional for authenticated users
   *   billing_name: 'John Doe',          // Optional for authenticated users
   *   quantity: 1                         // Optional, defaults to 1
   * });
   * window.location.href = session.checkout_url;
   */
  async create(data: CreateCheckoutData): Promise<CheckoutSession> {
    // Skip auth check if billing_email is provided (guest checkout)
    const skipAuthCheck = !!data.billing_email;

    const response = await this.http.post<{ checkout: CheckoutSession }>(
      '/api/user/checkout',
      data,
      skipAuthCheck
    );

    // Clear both orders and subscriptions cache after checkout
    // to ensure fresh data when user returns from payment
    this.orders.clearCache();
    this.subscriptions.clearCache();

    return response.checkout;
  }

  /**
   * Get customer portal URL for managing subscriptions
   *
   * @example
   * const portal = await paycan.checkout.getPortalUrl();
   * window.location.href = portal.url;
   */
  async getPortalUrl(returnUrl?: string): Promise<{ url: string; session_id?: string }> {
    const response = await this.http.post<{ portal: { url: string; session_id?: string } }>(
      '/api/user/checkout/portal',
      returnUrl ? { return_url: returnUrl } : undefined
    );
    return response.portal;
  }

  /**
   * Preview checkout totals
   *
   * Get a preview of the order totals. Tax is calculated and collected by the
   * payment gateway at checkout (e.g. Stripe Tax), so previews are pre-tax
   * before creating the actual checkout session.
   *
   * @example
   * const preview = await paycan.checkout.preview({
   *   product_price_id: 1,
   *   gateway: 'stripe',
   *   quantity: 2,
   *   billing_country: 'US',
   *   billing_state: 'CA'
   * });
   * console.log('Total:', preview.selected_price.final_price);
   */
  async preview(params: GetCheckoutPreviewParams): Promise<CheckoutPreviewResponse> {
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

    // Preview is now public endpoint, skip auth check
    return this.http.get<CheckoutPreviewResponse>(
      `/api/user/checkout/preview?${queryParams.toString()}`,
      undefined,
      true
    );
  }
}
