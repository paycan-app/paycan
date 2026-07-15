/**
 * PayCan Orders List Modal Web Component
 *
 * A framework-agnostic modal to display user orders
 */
import type { PayCan } from '../paycan';
export interface OrdersModalOptions {
    theme?: 'light' | 'dark' | 'auto';
    onClose?: () => void;
    onError?: (error: Error) => void;
}
export declare class OrdersModal {
    private sdk;
    private options;
    private container;
    private shadowRoot;
    private overlay;
    private modal;
    private orders;
    private loading;
    private currentPage;
    private totalPages;
    constructor(sdk: PayCan, options?: OrdersModalOptions);
    /**
     * Open the modal and load orders
     */
    open(): Promise<void>;
    /**
     * Close and destroy the modal
     */
    close(): void;
    /**
     * Load orders from API
     */
    private loadOrders;
    /**
     * Create modal DOM structure with Shadow DOM
     */
    private createModal;
    /**
     * Refresh modal content
     */
    private refreshModal;
    /**
     * Handle escape key press
     */
    private handleEscapeKey;
    /**
     * Detect if dark mode should be used
     */
    private isDarkMode;
    /**
     * Get modal HTML content
     */
    private getModalContent;
    /**
     * Get orders list HTML
     */
    private getOrdersList;
    /**
     * Get subscription card HTML
     */
    private getOrderCard;
    /**
     * Get loading state HTML
     */
    private getLoadingState;
    /**
     * Get pagination HTML
     */
    private getPagination;
    /**
     * Attach event listeners
     */
    private attachEventListeners;
    /**
     * Show toast notification
     */
    private showToast;
    /**
     * Handle errors
     */
    private handleError;
    /**
     * Format price
     */
    private formatPrice;
    /**
     * Format date
     */
    private formatDate;
    /**
     * Format billing period
     */
    private getBillingPeriodText;
    /**
     * Format status
     */
    private formatStatus;
    /**
     * Get status class
     */
    private getStatusClass;
    /**
     * Format gateway name
     */
    private formatGateway;
    /**
     * Escape HTML to prevent XSS
     */
    private escapeHtml;
    /**
     * Get all modal CSS styles
     */
    private getStyles;
}
//# sourceMappingURL=orders-modal.d.ts.map