/**
 * PayCan Products List Modal Web Component
 *
 * A framework-agnostic modal to display available products/plans
 * Integrates with checkout modal for purchasing or changing subscriptions
 */
import type { PayCan } from '../paycan';
import type { Product } from '../types';
export interface ProductsModalOptions {
    theme?: 'light' | 'dark' | 'auto';
    type?: 'physical' | 'digital' | 'service' | 'subscription';
    currentPriceId?: number | string;
    subscriptionId?: number | string;
    onClose?: () => void;
    onError?: (error: Error) => void;
    onProductSelected?: (product: Product) => void;
}
export declare class ProductsModal {
    private sdk;
    private options;
    private container;
    private shadowRoot;
    private overlay;
    private modal;
    private products;
    private loading;
    private currentPage;
    private totalPages;
    constructor(sdk: PayCan, options?: ProductsModalOptions);
    /**
     * Open the modal and load products
     */
    open(): Promise<void>;
    /**
     * Close and destroy the modal
     */
    close(): void;
    /**
     * Load products from API
     */
    private loadProducts;
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
     * Get products grid HTML
     */
    private getProductsGrid;
    /**
     * Get product card HTML
     */
    private getProductCard;
    /**
     * Get button text based on price type and current status
     */
    private getPriceButtonText;
    /**
     * Get button class for price based on index
     */
    private getPriceButtonClass;
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
     * Handle product selection - change subscription or open checkout
     */
    private handleProductSelection;
    /**
     * Show toast notification
     */
    private showToast;
    /**
     * Handle errors
     */
    private handleError;
    /**
     * Get all modal CSS styles
     */
    private getStyles;
}
//# sourceMappingURL=products-modal.d.ts.map