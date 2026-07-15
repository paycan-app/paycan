/**
 * PayCan Orders List Modal Web Component
 *
 * A framework-agnostic modal to display user orders
 */

import type { PayCan } from '../paycan';
import { ToastHelper } from './shared-styles';

export interface OrdersModalOptions {
  theme?: 'light' | 'dark' | 'auto';
  onClose?: () => void;
  onError?: (error: Error) => void;
}

interface Order {
  id: number;
  order_number: string;
  status: string;
  gateway: string;
  total: number;
  currency: string;
  tax: number;
  quantity: number;
  product: {
    id: string;
    name: string;
    description?: string;
  };
  product_price: {
    id: string;
    title: string;
    amount: number;
    currency: string;
    billing_period: string;
  };
  created_at: string;
}

export class OrdersModal {
  private sdk: PayCan;
  private options: OrdersModalOptions;
  private container: HTMLElement | null = null;
  private shadowRoot: ShadowRoot | null = null;
  private overlay: HTMLElement | null = null;
  private modal: HTMLElement | null = null;
  private orders: Order[] = [];
  private loading: boolean = false;
  private currentPage: number = 1;
  private totalPages: number = 1;

  constructor(sdk: PayCan, options: OrdersModalOptions = {}) {
    this.sdk = sdk;
    this.options = options;
  }

  /**
   * Open the modal and load orders
   */
  async open(): Promise<void> {
    try {
      await this.loadOrders();
      this.createModal();
    } catch (error) {
      this.handleError(error as Error);
    }
  }

  /**
   * Close and destroy the modal
   */
  close(): void {
    if (this.container) {
      this.container.remove();
      this.container = null;
      this.shadowRoot = null;
      this.overlay = null;
      this.modal = null;
    }
    document.removeEventListener('keydown', this.handleEscapeKey);

    if (this.options.onClose) {
      this.options.onClose();
    }
  }

  /**
   * Load orders from API
   */
  private async loadOrders(page: number = 1): Promise<void> {
    this.loading = true;
    try {
      const response = await this.sdk.get(`/api/user/orders?page=${page}&per_page=10&include=product,productPrice`);
      this.orders = response.data;
      this.currentPage = response.meta?.current_page || 1;
      this.totalPages = response.meta?.last_page || 1;
      this.loading = false;
    } catch (error) {
      this.loading = false;
      throw error;
    }
  }

  /**
   * Create modal DOM structure with Shadow DOM
   */
  private createModal(): void {
    const isDark = this.isDarkMode();
    const themeClass = isDark ? 'paycan-theme-dark' : 'paycan-theme-light';

    // Create container element for Shadow DOM
    this.container = document.createElement('div');
    this.container.setAttribute('id', 'paycan-orders-modal');

    // Attach Shadow DOM
    this.shadowRoot = this.container.attachShadow({ mode: 'open' });

    // Create style element inside shadow root
    const styleEl = document.createElement('style');
    styleEl.textContent = this.getStyles();
    this.shadowRoot.appendChild(styleEl);

    // Create overlay inside shadow root
    this.overlay = document.createElement('div');
    this.overlay.className = `paycan-modal-overlay ${themeClass}`;

    // Create modal container
    this.modal = document.createElement('div');
    this.modal.className = `paycan-modal paycan-modal-wide ${themeClass}`;

    this.modal.innerHTML = this.getModalContent();

    this.overlay.appendChild(this.modal);
    this.shadowRoot.appendChild(this.overlay);

    // Append container to body
    document.body.appendChild(this.container);

    // Add event listeners
    this.attachEventListeners();

    // Close on overlay click
    this.overlay.addEventListener('click', (e) => {
      if (e.target === this.overlay) {
        this.close();
      }
    });

    // Close on Escape key
    document.addEventListener('keydown', this.handleEscapeKey);
  }

  /**
   * Refresh modal content
   */
  private refreshModal(): void {
    if (this.modal) {
      this.modal.innerHTML = this.getModalContent();
      this.attachEventListeners();
    }
  }

  /**
   * Handle escape key press
   */
  private handleEscapeKey = (e: KeyboardEvent): void => {
    if (e.key === 'Escape') {
      this.close();
    }
  };

  /**
   * Detect if dark mode should be used
   */
  private isDarkMode(): boolean {
    const theme = this.options.theme || 'auto';

    if (theme === 'dark') {
      return true;
    } else if (theme === 'light') {
      return false;
    } else {
      return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    }
  }

  /**
   * Get modal HTML content
   */
  private getModalContent(): string {
    return `
      <div class="paycan-modal-header">
        <h2 class="paycan-modal-title">My Orders</h2>
        <button class="paycan-close-btn" aria-label="Close">×</button>
      </div>
      <div class="paycan-toast">
        <div class="paycan-toast-content"></div>
      </div>
      <div class="paycan-modal-body">
        <div class="paycan-body-header">
          <a href="#" class="paycan-link" data-action="view-transactions">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Payment History
          </a>
        </div>
        ${this.loading ? this.getLoadingState() : this.getOrdersList()}
      </div>
      ${this.getPagination()}
    `;
  }

  /**
   * Get orders list HTML
   */
  private getOrdersList(): string {
    if (this.orders.length === 0) {
      return `
        <div class="paycan-empty-state">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <p>No orders found</p>
        </div>
      `;
    }

    return `
      <div class="paycan-items-grid">
        ${this.orders.map(sub => this.getOrderCard(sub)).join('')}
      </div>
    `;
  }

  /**
   * Get subscription card HTML
   */
  private getOrderCard(order: Order): string {
    const statusClass = this.getStatusClass(order.status);
    const statusText = this.formatStatus(order.status);

    return `
      <div class="paycan-card">
        <div class="paycan-card-header">
          <div>
            <h3 class="paycan-card-title">${this.escapeHtml(order.product.name)}</h3>
            <p class="paycan-card-subtitle">${this.escapeHtml(order.product_price.title)}</p>
          </div>
          <span class="paycan-badge paycan-badge-${statusClass}">${statusText}</span>
        </div>
        <div class="paycan-card-body">
          <div class="paycan-info-grid">
            <div class="paycan-info-item">
              <span class="paycan-info-label">Total</span>
              <span class="paycan-info-value">${this.formatPrice(order.total, order.currency)}</span>
            </div>
            <div class="paycan-info-item">
              <span class="paycan-info-label">Order Number</span>
              <span class="paycan-info-value">${this.escapeHtml(order.order_number)}</span>
            </div>
            <div class="paycan-info-item">
              <span class="paycan-info-label">Payment Method</span>
              <span class="paycan-info-value">${this.formatGateway(order.gateway)}</span>
            </div>
            <div class="paycan-info-item">
              <span class="paycan-info-label">Quantity</span>
              <span class="paycan-info-value">${order.quantity}</span>
            </div>
            <div class="paycan-info-item">
              <span class="paycan-info-label">Order Date</span>
              <span class="paycan-info-value">${this.formatDate(order.created_at)}</span>
            </div>
            <div class="paycan-info-item">
              <span class="paycan-info-label">Billing Period</span>
              <span class="paycan-info-value">${this.getBillingPeriodText(order.product_price.billing_period)}</span>
            </div>
          </div>
        </div>
      </div>
    `;
  }

  /**
   * Get loading state HTML
   */
  private getLoadingState(): string {
    return `
      <div class="paycan-loading-state">
        <div class="paycan-spinner"></div>
        <p>Loading...</p>
      </div>
    `;
  }

  /**
   * Get pagination HTML
   */
  private getPagination(): string {
    if (this.totalPages <= 1) {
      return '';
    }

    return `
      <div class="paycan-modal-footer">
        <div class="paycan-pagination">
          <button class="paycan-btn paycan-btn-secondary paycan-btn-sm" data-action="prev-page" ${this.currentPage === 1 ? 'disabled' : ''}>
            Previous
          </button>
          <span class="paycan-page-info">Page ${this.currentPage} of ${this.totalPages}</span>
          <button class="paycan-btn paycan-btn-secondary paycan-btn-sm" data-action="next-page" ${this.currentPage === this.totalPages ? 'disabled' : ''}>
            Next
          </button>
        </div>
      </div>
    `;
  }

  /**
   * Attach event listeners
   */
  private attachEventListeners(): void {
    if (!this.modal) return;

    // Close button
    const closeBtn = this.modal.querySelector('.paycan-close-btn');
    closeBtn?.addEventListener('click', () => this.close());

    // Action buttons and links
    const actionButtons = this.modal.querySelectorAll('[data-action]');
    actionButtons.forEach(btn => {
      btn.addEventListener('click', async (e) => {
        e.preventDefault();
        const target = e.currentTarget as HTMLElement;
        const action = target.dataset.action;

        if (!action) return;

        try {
          switch (action) {
            case 'view-transactions':
              // Open transactions modal
              const { TransactionsModal } = await import('./transactions-modal');
              const txModal = new TransactionsModal(this.sdk, this.options);
              txModal.open();
              break;
            case 'prev-page':
              if (this.currentPage > 1) {
                await this.loadOrders(this.currentPage - 1);
                this.refreshModal();
              }
              break;
            case 'next-page':
              if (this.currentPage < this.totalPages) {
                await this.loadOrders(this.currentPage + 1);
                this.refreshModal();
              }
              break;
          }
        } catch (error) {
          this.handleError(error as Error);
        }
      });
    });
  }

  /**
   * Show toast notification
   */
  private showToast(message: string, type: 'success' | 'error' | 'info' = 'info'): void {
    ToastHelper.showToast(this.modal, message, type);
  }

  /**
   * Handle errors
   */
  private handleError(error: Error): void {
    this.showToast(error.message, 'error');
    if (this.options.onError) {
      this.options.onError(error);
    }
  }

  /**
   * Format price
   */
  private formatPrice(amount: number, currency?: string): string {
    if (!currency) {
      return amount.toFixed(2);
    }
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: currency.toUpperCase(),
    }).format(amount);
  }

  /**
   * Format date
   */
  private formatDate(date: string): string {
    return new Date(date).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  }

  /**
   * Format billing period
   */
  private getBillingPeriodText(period: string): string {
    const periodMap: Record<string, string> = {
      'day': 'day',
      'week': 'week',
      'month': 'month',
      'year': 'year',
    };
    return periodMap[period] || period;
  }

  /**
   * Format status
   */
  private formatStatus(status: string): string {
    return status.charAt(0).toUpperCase() + status.slice(1).replace(/_/g, ' ');
  }

  /**
   * Get status class
   */
  private getStatusClass(status: string): string {
    const statusMap: Record<string, string> = {
      'active': 'success',
      'completed': 'success',
      'succeeded': 'success',
      'pending': 'warning',
      'processing': 'warning',
      'cancelled': 'error',
      'failed': 'error',
      'refunded': 'info',
    };
    return statusMap[status] || 'default';
  }

  /**
   * Format gateway name
   */
  private formatGateway(gateway: string): string {
    const gatewayMap: Record<string, string> = {
      'stripe': 'Stripe',
      'paypal': 'PayPal',
    };
    return gatewayMap[gateway] || gateway;
  }

  /**
   * Escape HTML to prevent XSS
   */
  private escapeHtml(text: string): string {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  /**
   * Get all modal CSS styles
   */
  private getStyles(): string {
    return `
      /* PayCan Orders Modal Styles */

      /* CSS Variables for Customization */
      .paycan-modal-overlay {
        --paycan-accent: #3b82f6;
        --paycan-accent-hover: #2563eb;
        --paycan-accent-light: #eff6ff;
        --paycan-accent-dark: #1e3a8a;
      }

      /* Overlay */
      .paycan-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 999999;
        padding: 1rem;
      }

      /* Modal Container */
      .paycan-modal {
        width: 100%;
        max-width: 600px;
        max-height: 90vh;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
      }

      .paycan-modal-wide {
        max-width: 800px;
      }

      /* Theme Colors */
      .paycan-modal.paycan-theme-light {
        background: #f9fafb;
        color: #111827;
      }

      .paycan-modal.paycan-theme-dark {
        background: #111827;
        color: #f9fafb;
      }

      /* Modal Header */
      .paycan-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid;
        flex-shrink: 0;
      }

      .paycan-theme-light .paycan-modal-header {
        border-bottom-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-modal-header {
        border-bottom-color: #374151;
      }

      .paycan-modal-title {
        font-size: 1.25rem;
        font-weight: 500;
        margin: 0;
      }

      .paycan-header-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
      }

      .paycan-link {
        font-size: 0.875rem;
        text-decoration: none;
        cursor: pointer;
        transition: opacity 0.2s;
      }

      .paycan-link:hover {
        opacity: 0.8;
      }

      .paycan-theme-light .paycan-link {
        color: #3b82f6;
      }

      .paycan-theme-dark .paycan-link {
        color: #60a5fa;
      }

      /* Close Button */
      .paycan-close-btn {
        background: none;
        border: none;
        font-size: 1.5rem;
        font-weight: 200;
        cursor: pointer;
        padding: 0;
        line-height: 1;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: background 0.15s;
        box-shadow: none;
      }

      .paycan-theme-light .paycan-close-btn {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-close-btn {
        color: #9ca3af;
      }

      .paycan-theme-light .paycan-close-btn:hover {
        background: #f3f4f6;
      }

      .paycan-theme-dark .paycan-close-btn:hover {
        background: #374151;
      }

      /* Back Button */
      .paycan-back-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        font-weight: 400;
        border-radius: 4px;
        transition: background 0.15s;
      }

      .paycan-theme-light .paycan-back-btn {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-back-btn {
        color: #9ca3af;
      }

      .paycan-theme-light .paycan-back-btn:hover {
        background: #f3f4f6;
      }

      .paycan-theme-dark .paycan-back-btn:hover {
        background: #374151;
      }

      /* Modal Body */
      .paycan-modal-body {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
      }

      .paycan-body-header {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 1rem;
      }

      .paycan-body-header .paycan-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
      }

      .paycan-body-header .paycan-link svg {
        flex-shrink: 0;
      }

      /* Modal Footer */
      .paycan-modal-footer {
        padding: 1rem 1.25rem;
        border-top: 1px solid;
        flex-shrink: 0;
      }

      .paycan-theme-light .paycan-modal-footer {
        border-top-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-modal-footer {
        border-top-color: #374151;
      }

      /* Items Grid */
      .paycan-items-grid {
        display: grid;
        gap: 1rem;
      }

      /* Card */
      .paycan-card {
        border-radius: 8px;
        border: 1px solid;
        overflow: hidden;
      }

      .paycan-theme-light .paycan-card {
        background: #ffffff;
        border-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-card {
        background: #1f2937;
        border-color: #4b5563;
      }

      .paycan-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding: 1rem;
        gap: 1rem;
        border-bottom: 1px solid;
      }

      .paycan-theme-light .paycan-card-header {
        border-bottom-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-card-header {
        border-bottom-color: #374151;
      }

      .paycan-card-title {
        font-size: 1rem;
        font-weight: 500;
        margin: 0 0 0.25rem 0;
      }

      .paycan-card-subtitle {
        font-size: 0.875rem;
        margin: 0;
      }

      .paycan-theme-light .paycan-card-subtitle {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-card-subtitle {
        color: #9ca3af;
      }

      .paycan-card-body {
        padding: 1rem;
      }

      .paycan-card-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.5rem;
        padding: 1rem;
        border-top: 1px solid;
      }

      .paycan-theme-light .paycan-card-footer {
        border-top-color: #e5e7eb;
        background: #f9fafb;
      }

      .paycan-theme-dark .paycan-card-footer {
        border-top-color: #374151;
        background: #111827;
      }

      /* Info Grid */
      .paycan-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 0.75rem;
      }

      .paycan-info-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
      }

      .paycan-info-label {
        font-size: 0.75rem;
        font-weight: 400;
      }

      .paycan-theme-light .paycan-info-label {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-info-label {
        color: #9ca3af;
      }

      .paycan-info-value {
        font-size: 0.875rem;
        font-weight: 400;
      }

      .paycan-info-warning .paycan-info-label,
      .paycan-info-warning .paycan-info-value {
        color: #f59e0b;
      }

      /* Badge */
      .paycan-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-weight: 500;
        flex-shrink: 0;
      }

      .paycan-badge-success {
        background: #d1fae5;
        color: #065f46;
      }

      .paycan-theme-dark .paycan-badge-success {
        background: #064e3b;
        color: #6ee7b7;
      }

      .paycan-badge-warning {
        background: #fef3c7;
        color: #92400e;
      }

      .paycan-theme-dark .paycan-badge-warning {
        background: #78350f;
        color: #fcd34d;
      }

      .paycan-badge-error {
        background: #fee2e2;
        color: #991b1b;
      }

      .paycan-theme-dark .paycan-badge-error {
        background: #7f1d1d;
        color: #fca5a5;
      }

      .paycan-badge-info {
        background: #dbeafe;
        color: #1e40af;
      }

      .paycan-theme-dark .paycan-badge-info {
        background: #1e3a8a;
        color: #93c5fd;
      }

      /* Transactions List */
      .paycan-transactions-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
      }

      .paycan-transaction-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid;
      }

      .paycan-theme-light .paycan-transaction-row {
        background: #ffffff;
        border-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-transaction-row {
        background: #1f2937;
        border-color: #4b5563;
      }

      .paycan-transaction-info {
        flex: 1;
        min-width: 0;
      }

      .paycan-transaction-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.5rem;
      }

      .paycan-transaction-number {
        font-size: 0.875rem;
        font-weight: 500;
      }

      .paycan-transaction-meta {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.75rem;
      }

      .paycan-theme-light .paycan-transaction-meta {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-transaction-meta {
        color: #9ca3af;
      }

      .paycan-transaction-description {
        font-size: 0.875rem;
        margin: 0.5rem 0 0 0;
      }

      .paycan-theme-light .paycan-transaction-description {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-transaction-description {
        color: #9ca3af;
      }

      .paycan-transaction-amount {
        font-size: 1rem;
        font-weight: 500;
        flex-shrink: 0;
      }

      /* Buttons */
      .paycan-btn {
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 400;
        cursor: pointer;
        border: 1px solid transparent;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
      }

      .paycan-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
      }

      .paycan-btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.8125rem;
      }

      .paycan-btn-primary {
        background: var(--paycan-accent);
        color: white;
      }

      .paycan-btn-primary:hover:not(:disabled) {
        background: var(--paycan-accent-hover);
      }

      .paycan-btn-secondary {
        border-color: #d1d5db;
      }

      .paycan-theme-light .paycan-btn-secondary {
        background: #ffffff;
        color: #374151;
      }

      .paycan-theme-dark .paycan-btn-secondary {
        background: #374151;
        color: #f9fafb;
        border-color: #4b5563;
      }

      .paycan-theme-light .paycan-btn-secondary:hover:not(:disabled) {
        background: #f9fafb;
      }

      .paycan-theme-dark .paycan-btn-secondary:hover:not(:disabled) {
        background: #4b5563;
      }

      .paycan-btn-danger {
        background: #ef4444;
        color: white;
      }

      .paycan-btn-danger:hover:not(:disabled) {
        background: #dc2626;
      }

      /* Empty State */
      .paycan-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem 1rem;
        text-align: center;
      }

      .paycan-empty-state svg {
        margin-bottom: 1rem;
      }

      .paycan-theme-light .paycan-empty-state svg {
        color: #9ca3af;
      }

      .paycan-theme-dark .paycan-empty-state svg {
        color: #6b7280;
      }

      .paycan-empty-state p {
        font-size: 0.875rem;
        margin: 0;
      }

      .paycan-theme-light .paycan-empty-state p {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-empty-state p {
        color: #9ca3af;
      }

      /* Loading State */
      .paycan-loading-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem 1rem;
      }

      .paycan-spinner {
        width: 40px;
        height: 40px;
        border: 3px solid;
        border-radius: 50%;
        border-top-color: var(--paycan-accent);
        animation: paycan-spin 0.6s linear infinite;
      }

      .paycan-theme-light .paycan-spinner {
        border-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-spinner {
        border-color: #374151;
      }

      @keyframes paycan-spin {
        to { transform: rotate(360deg); }
      }

      .paycan-loading-state p {
        margin-top: 1rem;
        font-size: 0.875rem;
      }

      .paycan-theme-light .paycan-loading-state p {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-loading-state p {
        color: #9ca3af;
      }

      /* Pagination */
      .paycan-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
      }

      .paycan-page-info {
        font-size: 0.875rem;
      }

      .paycan-theme-light .paycan-page-info {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-page-info {
        color: #9ca3af;
      }

      /* Toast Notification */
      .paycan-toast {
        position: absolute;
        top: 5rem;
        left: 50%;
        transform: translateX(-50%) translateY(-100px);
        padding: 0.75rem 1rem;
        border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        opacity: 0;
        transition: all 0.3s;
        pointer-events: none;
        z-index: 1000;
        max-width: 90%;
      }

      .paycan-toast.show {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
      }

      .paycan-toast-content {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
      }

      .paycan-toast-success {
        background: #10b981;
        color: white;
      }

      .paycan-toast-error {
        background: #ef4444;
        color: white;
      }

      .paycan-toast-info {
        background: #3b82f6;
        color: white;
      }

      /* Responsive */
      @media (max-width: 640px) {
        .paycan-modal {
          max-width: 100%;
        }

        .paycan-info-grid {
          grid-template-columns: 1fr;
        }

        .paycan-transaction-row {
          flex-direction: column;
          align-items: flex-start;
        }

        .paycan-transaction-amount {
          align-self: flex-end;
        }
      }
    `;
  }
}
