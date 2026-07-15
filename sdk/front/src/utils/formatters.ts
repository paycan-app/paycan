/**
 * Shared utility functions for formatting data across PayCan SDK components
 * This module eliminates code duplication and ensures consistent formatting
 */

export class Formatters {
  /**
   * Format price with currency
   */
  static formatPrice(amount: number, currency?: string): string {
    if (!currency) {
      return amount.toFixed(2);
    }
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: currency.toUpperCase(),
    }).format(amount);
  }

  /**
   * Format date with optional time
   */
  static formatDate(date: string, includeTime = false): string {
    const options: Intl.DateTimeFormatOptions = {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      ...(includeTime && { hour: '2-digit', minute: '2-digit' })
    };
    return new Date(date).toLocaleDateString('en-US', options);
  }

  /**
   * Format billing period text
   */
  static getBillingPeriodText(period: string): string {
    if (period === 'once') return 'one-time';
    
    // Handle different period formats
    const periodMap: Record<string, string> = {
      'day': 'day',
      'week': 'week', 
      'month': 'month',
      'year': 'year'
    };
    
    return periodMap[period] || period;
  }

  /**
   * Format status text (capitalize and replace underscores)
   */
  static formatStatus(status: string): string {
    return status.charAt(0).toUpperCase() + status.slice(1).replace(/_/g, ' ');
  }

  /**
   * Format payment gateway name
   */
  static formatGateway(gateway: string): string {
    const gatewayMap: Record<string, string> = {
      'stripe': 'Stripe',
      'paypal': 'PayPal',
      'razorpay': 'Razorpay',
      'paddle': 'Paddle'
    };
    return gatewayMap[gateway] || gateway.charAt(0).toUpperCase() + gateway.slice(1);
  }

  /**
   * Escape HTML to prevent XSS attacks
   */
  static escapeHtml(text: string): string {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  /**
   * Trim text to specified length with ellipsis
   */
  static trimText(text: string, maxLength: number): string {
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength).trim() + '...';
  }

  /**
   * Trim text to specified word count
   */
  static trimWords(text: string, maxWords: number): string {
    const words = text.split(' ');
    if (words.length <= maxWords) return text;
    return words.slice(0, maxWords).join(' ') + '...';
  }

  /**
   * Validate email format
   */
  static isValidEmail(email: string): boolean {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }
}