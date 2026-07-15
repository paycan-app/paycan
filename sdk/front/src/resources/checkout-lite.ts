/**
 * Checkout Resource (Lite)
 *
 * API-only checkout operations without Orders/Subscriptions cache deps
 */
import type { HttpClient } from '../http-client';
import type {
  CheckoutSession,
  CreateCheckoutData,
  GetCheckoutPreviewParams,
  CheckoutPreviewResponse,
} from '../types';

export class CheckoutLite {
  constructor(private http: HttpClient) {}

  async create(data: CreateCheckoutData): Promise<CheckoutSession> {
    // Always skip auth check for checkout endpoint
    // The endpoint supports both authenticated users (will use their token if valid)
    // and guest users (will create user based on billing_email)
    // This prevents 401 errors when a user has an expired token in localStorage
    const response = await this.http.post<{ checkout: CheckoutSession }>(
      '/api/user/checkout',
      data,
      true // skipAuthCheck
    );

    return response.checkout;
  }

  async getPortalUrl(returnUrl?: string): Promise<{ url: string; session_id?: string }> {
    const response = await this.http.post<{ portal: { url: string; session_id?: string } }>(
      '/api/user/checkout/portal',
      returnUrl ? { return_url: returnUrl } : undefined
    );
    return response.portal;
  }

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

    // Public endpoint; skip auth check
    return this.http.get<CheckoutPreviewResponse>(
      `/api/user/checkout/preview?${queryParams.toString()}`,
      undefined,
      true
    );
  }
}