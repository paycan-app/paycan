/**
 * Shared modal utility functions for PayCan SDK components
 * This module provides common modal functionality
 */
export declare class ModalHelpers {
    /**
     * Check if dark mode is enabled
     */
    static isDarkMode(): boolean;
    /**
     * Generate loading state HTML
     */
    static getLoadingState(): string;
    /**
     * Generate empty state HTML
     */
    static getEmptyState(message: string, icon?: string): string;
    /**
     * Generate pagination HTML
     */
    static getPagination(currentPage: number, totalPages: number): string;
    /**
     * Create modal container with shadow DOM
     */
    static createModalContainer(id: string): {
        container: HTMLElement;
        shadowRoot: ShadowRoot;
    };
    /**
     * Remove modal container
     */
    static removeModalContainer(container: HTMLElement | null): void;
}
//# sourceMappingURL=modal-helpers.d.ts.map