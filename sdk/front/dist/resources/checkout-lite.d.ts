/**
 * Checkout Resource (Lite)
 *
 * API-only checkout operations without Orders/Subscriptions cache deps
 */
import type { HttpClient } from '../http-client';
import type { CheckoutSession, CreateCheckoutData, GetCheckoutPreviewParams, CheckoutPreviewResponse } from '../types';
export declare class CheckoutLite {
    private http;
    constructor(http: HttpClient);
    create(data: CreateCheckoutData): Promise<CheckoutSession>;
    getPortalUrl(returnUrl?: string): Promise<{
        url: string;
        session_id?: string;
    }>;
    preview(params: GetCheckoutPreviewParams): Promise<CheckoutPreviewResponse>;
}
//# sourceMappingURL=checkout-lite.d.ts.map