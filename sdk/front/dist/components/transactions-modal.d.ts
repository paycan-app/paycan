/**
 * PayCan Transactions List Modal Web Component
 *
 * A framework-agnostic modal to display user transactions
 */
import type { PayCan } from '../paycan';
export interface TransactionsModalOptions {
    theme?: 'light' | 'dark' | 'auto';
    onClose?: () => void;
    onError?: (error: Error) => void;
}
export declare class TransactionsModal {
    private sdk;
    private options;
    private container;
    private shadowRoot;
    private overlay;
    private modal;
    private transactions;
    private loading;
    private currentPage;
    private totalPages;
    constructor(sdk: PayCan, options?: TransactionsModalOptions);
    /**
     * Open the modal and load transactions
     */
    open(): Promise<void>;
    /**
     * Close and destroy the modal
     */
    close(): void;
    /**
     * Load transactions from API
     */
    private loadTransactions;
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
     * Get transactions list HTML
     */
    private getTransactionsList;
    /**
     * Get transaction row HTML
     */
    private getTransactionRow;
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
     * Format transaction type
     */
    private formatTransactionType;
    /**
     * Escape HTML to prevent XSS
     */
    private escapeHtml;
    /**
     * Get all modal CSS styles
     */
    private getStyles;
}
//# sourceMappingURL=transactions-modal.d.ts.map