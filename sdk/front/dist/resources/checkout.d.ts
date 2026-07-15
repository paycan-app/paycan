/**
 * Checkout Resource
 *
 * Handle checkout and payment portal operations
 */
import type { HttpClient } from '../http-client';
import type { CheckoutSession, CreateCheckoutData, GetCheckoutPreviewParams, CheckoutPreviewResponse } from '../types';
import type { Orders } from './orders';
import type { Subscriptions } from './subscriptions';
export declare class Checkout {
    private http;
    private orders;
    private subscriptions;
    constructor(http: HttpClient, orders: Orders, subscriptions: Subscriptions);
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
    create(data: CreateCheckoutData): Promise<CheckoutSession>;
    /**
     * Get customer portal URL for managing subscriptions
     *
     * @example
     * const portal = await paycan.checkout.getPortalUrl();
     * window.location.href = portal.url;
     */
    getPortalUrl(returnUrl?: string): Promise<{
        url: string;
        session_id?: string;
    }>;
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
    preview(params: GetCheckoutPreviewParams): Promise<CheckoutPreviewResponse>;
}
//# sourceMappingURL=checkout.d.ts.map