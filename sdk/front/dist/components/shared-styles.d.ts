/**
 * Shared Styles for PayCan Modals
 *
 * This module contains reusable CSS styles for all PayCan modals
 * to ensure consistency and reduce code duplication.
 */
/**
 * Get base modal styles (overlay, container, header, footer, etc.)
 */
export declare function getBaseModalStyles(): string;
/**
 * Get toast notification styles
 */
export declare function getToastStyles(): string;
/**
 * Get common button styles
 */
export declare function getButtonStyles(): string;
/**
 * Get common card styles
 */
export declare function getCardStyles(): string;
/**
 * Get common badge styles
 */
export declare function getBadgeStyles(): string;
/**
 * Get loading state styles
 */
export declare function getLoadingStyles(): string;
/**
 * Get empty state styles
 */
export declare function getEmptyStateStyles(): string;
/**
 * Get all shared styles combined
 */
export declare function getAllSharedStyles(): string;
/**
 * Base toast helper for consistent behavior across all modals
 */
export declare class ToastHelper {
    /**
     * Show toast notification
     */
    static showToast(modal: HTMLElement | null, message: string, type?: 'success' | 'error' | 'info'): void;
    /**
     * Escape HTML to prevent XSS
     */
    private static escapeHtml;
}
//# sourceMappingURL=shared-styles.d.ts.map