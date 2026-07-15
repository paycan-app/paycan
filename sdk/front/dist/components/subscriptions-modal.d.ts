/**
 * PayCan Subscriptions List Modal Web Component
 *
 * A framework-agnostic modal to display user subscriptions
 */
import type { PayCan } from '../paycan';
export interface SubscriptionsModalOptions {
    theme?: 'light' | 'dark' | 'auto';
    onClose?: () => void;
    onError?: (error: Error) => void;
}
export declare class SubscriptionsModal {
    private sdk;
    private options;
    private container;
    private shadowRoot;
    private overlay;
    private modal;
    private subscriptions;
    private loading;
    private currentPage;
    private totalPages;
    constructor(sdk: PayCan, options?: SubscriptionsModalOptions);
    /**
     * Open the modal and load subscriptions
     */
    open(): Promise<void>;
    /**
     * Close and destroy the modal
     */
    close(): void;
    /**
     * Load subscriptions from API
     */
    private loadSubscriptions;
    /**
     * Cancel a subscription
     */
    private cancelSubscription;
    /**
     * Resume a subscription
     */
    private resumeSubscription;
    /**
     * Handle changing subscription plan
     */
    private handleChangePlan;
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
     * Get subscriptions list HTML
     */
    private getSubscriptionsList;
    /**
     * Get subscription card HTML
     */
    private getSubscriptionCard;
    /**
     * Get subscription action buttons
     */
    private getSubscriptionActions;
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
//# sourceMappingURL=subscriptions-modal.d.ts.map