/**
 * Shared utility functions for formatting data across PayCan SDK components
 * This module eliminates code duplication and ensures consistent formatting
 */
export declare class Formatters {
    /**
     * Format price with currency
     */
    static formatPrice(amount: number, currency?: string): string;
    /**
     * Format date with optional time
     */
    static formatDate(date: string, includeTime?: boolean): string;
    /**
     * Format billing period text
     */
    static getBillingPeriodText(period: string): string;
    /**
     * Format status text (capitalize and replace underscores)
     */
    static formatStatus(status: string): string;
    /**
     * Format payment gateway name
     */
    static formatGateway(gateway: string): string;
    /**
     * Escape HTML to prevent XSS attacks
     */
    static escapeHtml(text: string): string;
    /**
     * Trim text to specified length with ellipsis
     */
    static trimText(text: string, maxLength: number): string;
    /**
     * Trim text to specified word count
     */
    static trimWords(text: string, maxWords: number): string;
    /**
     * Validate email format
     */
    static isValidEmail(email: string): boolean;
}
//# sourceMappingURL=formatters.d.ts.map