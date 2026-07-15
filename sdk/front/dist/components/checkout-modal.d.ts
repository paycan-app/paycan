/**
 * PayCan Checkout Modal Web Component
 *
 * A framework-agnostic checkout modal that can be embedded anywhere
 */
import type { PayCan } from '../paycan';
export interface CheckoutModalOptions {
    productId?: number | string;
    priceId?: number | string;
    theme?: 'light' | 'dark' | 'auto';
    onSuccess?: (checkoutUrl: string) => void;
    onCancel?: () => void;
    onError?: (error: Error) => void;
}
export declare class CheckoutModal {
    private sdk;
    private options;
    private container;
    private shadowRoot;
    private overlay;
    private modal;
    private previewData;
    private selectedPriceId;
    private selectedGateway;
    private loading;
    constructor(sdk: PayCan, options: CheckoutModalOptions);
    /**
     * Open the modal and load preview data
     */
    open(): Promise<void>;
    /**
     * Close and destroy the modal
     */
    close(): void;
    /**
     * Load preview data from API
     */
    private loadPreview;
    /**
     * Create modal DOM structure with Shadow DOM
     */
    private createModal;
    /**
     * Get all modal CSS styles
     */
    private getStyles;
    /**
     * Detect if dark mode should be used
     */
    private isDarkMode;
    /**
     * Get modal HTML content
     */
    private getModalContent;
    /**
     * Get guest email field HTML
     */
    private getGuestEmailField;
    /**
     * Get price selection field HTML
     */
    private getPriceSelectionField;
    /**
     * Get price breakdown HTML
     */
    private getPriceBreakdown;
    /**
     * Get payment methods field HTML
     */
    private getPaymentMethodsField;
    /**
     * Attach event listeners to modal elements
     */
    private attachEventListeners;
    /**
     * Handle price change
     */
    private handlePriceChange;
    /**
     * Handle checkout button click
     */
    private handleCheckout;
    /**
     * Update checkout button state
     */
    private updateCheckoutButton;
    /**
     * Handle escape key press
     */
    private handleEscapeKey;
    /**
     * Show modal with animation
     */
    private show;
    /**
     * Show toast notification
     */
    private showToast;
    /**
     * Handle errors
     */
    private handleError;
    /**
     * Cleanup
     */
    destroy(): void;
}
//# sourceMappingURL=checkout-modal.d.ts.map