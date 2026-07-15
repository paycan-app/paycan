/**
 * PayCan Products List Modal Web Component
 *
 * A framework-agnostic modal to display available products/plans
 * Integrates with checkout modal for purchasing or changing subscriptions
 */

import type { PayCan } from '../paycan';
import type { Product } from '../types';
import { getAllSharedStyles, ToastHelper } from './shared-styles';
import { CheckoutModal } from './checkout-modal';
import { Formatters } from '../utils/formatters';
import { ModalHelpers } from '../utils/modal-helpers';
import { EventHandlers } from '../utils/event-handlers';

export interface ProductsModalOptions {
  theme?: 'light' | 'dark' | 'auto';
  type?: 'physical' | 'digital' | 'service' | 'subscription';
  currentPriceId?: number | string; // The current price ID (for highlighting current plan)
  subscriptionId?: number | string; // The subscription ID to change
  onClose?: () => void;
  onError?: (error: Error) => void;
  onProductSelected?: (product: Product) => void;
}

export class ProductsModal {
  private sdk: PayCan;
  private options: ProductsModalOptions;
  private container: HTMLElement | null = null;
  private shadowRoot: ShadowRoot | null = null;
  private overlay: HTMLElement | null = null;
  private modal: HTMLElement | null = null;
  private products: Product[] = [];
  private loading: boolean = false;
  private currentPage: number = 1;
  private totalPages: number = 1;

  constructor(sdk: PayCan, options: ProductsModalOptions = {}) {
    this.sdk = sdk;
    this.options = options;
  }

  /**
   * Open the modal and load products
   */
  async open(): Promise<void> {
    // Set loading state
    this.loading = true;

    // Create modal first with loading state
    this.createModal();

    // Then load products
    try {
      await this.loadProducts();
      this.refreshModal();
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
   * Load products from API
   */
  private async loadProducts(page: number = 1): Promise<void> {
    this.loading = true;
    try {
      const params: any = {
        page,
        per_page: 12,
        include: 'prices',
      };

      if (this.options.type) {
        params['filter[type]'] = this.options.type;
      }

      const response = await this.sdk.products.list(params);
      this.products = response.data;
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
    this.container.setAttribute('id', 'paycan-products-modal');

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

    // Show modal with animation
    setTimeout(() => {
      this.overlay?.classList.add('paycan-show');
      this.modal?.classList.add('paycan-show');
    }, 10);
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
  private handleEscapeKey = EventHandlers.createEscapeHandler(() => this.close());

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
      return ModalHelpers.isDarkMode();
    }
  }

  /**
   * Get modal HTML content
   */
  private getModalContent(): string {
    const title = this.options.subscriptionId
      ? 'Change Subscription Plan'
      : this.options.type === 'subscription'
        ? 'Available Plans'
        : 'Available Products';

    return `
      <div class="paycan-modal-header">
        <h2 class="paycan-modal-title">${title}</h2>
        <button class="paycan-close-btn" aria-label="Close">×</button>
      </div>
      <div class="paycan-toast">
        <div class="paycan-toast-content"></div>
      </div>
      <div class="paycan-modal-body">
        ${this.loading ? this.getLoadingState() : this.getProductsGrid()}
      </div>
      ${this.getPagination()}
    `;
  }

  /**
   * Get products grid HTML
   */
  private getProductsGrid(): string {
    if (this.products.length === 0) {
      return `
        <div class="paycan-empty-state">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <p>No products available</p>
        </div>
      `;
    }

    return `
      <div class="paycan-products-grid">
        ${this.products.map(product => this.getProductCard(product)).join('')}
      </div>
    `;
  }

  /**
   * Get product card HTML
   */
  private getProductCard(product: any): string {
    const prices = product.prices || [];

    return `
      <div class="paycan-product-card">
        <div class="paycan-product-header">
          <h3 class="paycan-product-title">${Formatters.escapeHtml(product.title)}</h3>
          ${product.description ? `<p class="paycan-product-description">${Formatters.escapeHtml(Formatters.trimText(product.description, 120))}</p>` : ''}
        </div>
        ${prices.length > 0 ? `
          <div class="paycan-prices-list">
            ${prices.map((price: any, index: number) => {
              const isCurrentPrice = !!(this.options.currentPriceId &&
                price.id.toString() === this.options.currentPriceId.toString());
              const buttonClass = isCurrentPrice ? 'paycan-btn-success' : this.getPriceButtonClass(index);
              const buttonText = this.getPriceButtonText(price, isCurrentPrice);

              return `
                <div class="paycan-price-row ${isCurrentPrice ? 'current' : ''}">
                  <div class="paycan-price-name">${Formatters.escapeHtml(price.title)}</div>
                  <div class="paycan-price-amount">
                    ${Formatters.formatPrice(price.amount, price.currency)}${price.billing_period && price.billing_period !== 'once' ? '/' + Formatters.getBillingPeriodText(price.billing_period) : ''}
                  </div>
                  <button class="paycan-btn ${buttonClass} paycan-btn-sm paycan-btn-push ${isCurrentPrice ? 'current-price' : ''}"
                          data-action="select-product"
                          data-product-id="${product.id}"
                          data-price-id="${price.id}"
                          ${isCurrentPrice ? 'disabled' : ''}>
                    ${buttonText}
                  </button>
                </div>
              `;
            }).join('')}
          </div>
        ` : '<p class="paycan-help-text">No pricing available</p>'}
      </div>
    `;
  }

  /**
   * Get button text based on price type and current status
   */
  private getPriceButtonText(price: any, isCurrentPrice: boolean): string {
    if (isCurrentPrice) {
      return '✓ Current Plan';
    }

    // Check if it's a subscription (has billing period other than 'once')
    if (price.billing_period && price.billing_period !== 'once') {
      return 'Subscribe';
    }

    // One-time purchase
    return 'Buy Now';
  }



  /**
   * Get button class for price based on index
   */
  private getPriceButtonClass(index: number): string {
    const classes = ['paycan-btn-primary', 'paycan-btn-info', 'paycan-btn-warning', 'paycan-btn-purple'];
    return classes[index % classes.length];
  }

  /**
   * Get loading state HTML
   */
  private getLoadingState(): string {
    return `
      <div class="paycan-loading-state">
        <div class="paycan-spinner"></div>
        <p>Loading products...</p>
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

    // Action buttons
    const actionButtons = this.modal.querySelectorAll('[data-action]');
    actionButtons.forEach(btn => {
      btn.addEventListener('click', async (e) => {
        e.preventDefault();
        const target = e.currentTarget as HTMLElement;
        const action = target.dataset.action;

        if (!action) return;

        try {
          switch (action) {
            case 'select-product':
              const productId = target.dataset.productId;
              const priceId = target.dataset.priceId;
              if (productId && priceId) {
                await this.handleProductSelection(productId, priceId);
              }
              break;
            case 'prev-page':
              if (this.currentPage > 1) {
                await this.loadProducts(this.currentPage - 1);
                this.refreshModal();
              }
              break;
            case 'next-page':
              if (this.currentPage < this.totalPages) {
                await this.loadProducts(this.currentPage + 1);
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
   * Handle product selection - change subscription or open checkout
   */
  private async handleProductSelection(productId: string, priceId: string): Promise<void> {
    const product = this.products.find(p => p.id.toString() === productId);

    if (!product) {
      this.showToast('Product not found', 'error');
      return;
    }

    // If we have a subscriptionId, change the subscription plan
    if (this.options.subscriptionId) {
      try {
        this.showToast('Changing subscription plan...', 'info');

        const result = await this.sdk.subscriptions.change(
          this.options.subscriptionId.toString(),
          {
            product_price_id: priceId,
            prorate: true,
          }
        );

        this.showToast(result.message || 'Subscription changed successfully!', 'success');

        // Call callback if provided
        if (this.options.onProductSelected) {
          this.options.onProductSelected(product);
        }

        // Close modal after successful change
        setTimeout(() => {
          this.close();
        }, 1500);

      } catch (error: any) {
        this.showToast(error.message || 'Failed to change subscription', 'error');
      }
      return;
    }

    // Otherwise, open checkout modal for new purchase
    // Call callback if provided
    if (this.options.onProductSelected) {
      this.options.onProductSelected(product);
    }

    // Close products modal
    this.close();

    // Open checkout modal
    const checkoutModal = new CheckoutModal(this.sdk, {
      productId: product.id,
      priceId: priceId,
      theme: this.options.theme,
      onSuccess: (checkoutUrl) => {
        // Redirect to checkout
        window.location.href = checkoutUrl;
      },
      onError: (error) => {
        console.error('Checkout error:', error);
        // Keep checkout modal open so user can fix validation errors
        // Don't reopen products modal
      },
    });

    await checkoutModal.open();
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
   * Get all modal CSS styles
   */
  private getStyles(): string {
    return `
      /* PayCan Products Modal Styles */

      ${getAllSharedStyles()}

      /* Products Modal Specific Styles */

      /* Products Grid */
      .paycan-products-grid {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
      }

      /* Product Card */
      .paycan-product-card {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        padding: 1.5rem;
        border-radius: 8px;
        border: 1px solid;
      }

      .paycan-theme-light .paycan-product-card {
        background: #ffffff;
        border-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-product-card {
        background: #1f2937;
        border-color: #374151;
      }

      /* Product Header */
      .paycan-product-header {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
      }

      .paycan-product-badge {
        display: inline-block;
        align-self: flex-start;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
      }

      .paycan-theme-light .paycan-product-badge {
        background: #f3f4f6;
        color: #374151;
      }

      .paycan-theme-dark .paycan-product-badge {
        background: #374151;
        color: #d1d5db;
      }

      .paycan-product-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0;
      }

      .paycan-theme-light .paycan-product-title {
        color: #111827;
      }

      .paycan-theme-dark .paycan-product-title {
        color: #f9fafb;
      }

      .paycan-product-description {
        font-size: 0.875rem;
        margin: 0;
        line-height: 1.5;
      }

      .paycan-theme-light .paycan-product-description {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-product-description {
        color: #9ca3af;
      }

      /* Prices List */
      .paycan-prices-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-top: 0.5rem;
      }

      /* Price Row - 3 Column Layout */
      .paycan-price-row {
        display: grid;
        grid-template-columns: 1fr auto auto;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem;
        border-radius: 6px;
        transition: background-color 0.2s ease;
      }

      .paycan-theme-light .paycan-price-row {
        background: #f9fafb;
      }

      .paycan-theme-dark .paycan-price-row {
        background: #111827;
      }

      .paycan-theme-light .paycan-price-row:hover:not(.current) {
        background: #f3f4f6;
      }

      .paycan-theme-dark .paycan-price-row:hover:not(.current) {
        background: #1f2937;
      }

      .paycan-theme-light .paycan-price-row.current {
        background: #dcfce7;
        border: 1px solid #86efac;
      }

      .paycan-theme-dark .paycan-price-row.current {
        background: #064e3b;
        border: 1px solid #065f46;
      }

      .paycan-price-name {
        font-size: 0.813rem;
        font-weight: 500;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
      }

      .paycan-theme-light .paycan-price-name {
        color: #374151;
      }

      .paycan-theme-dark .paycan-price-name {
        color: #d1d5db;
      }

      .paycan-price-amount {
        font-size: 0.813rem;
        font-weight: 600;
        white-space: nowrap;
      }

      .paycan-theme-light .paycan-price-amount {
        color: #111827;
      }

      .paycan-theme-dark .paycan-price-amount {
        color: #f9fafb;
      }

      .paycan-price-row .paycan-btn {
        min-width: 100px;
      }

      /* Responsive */
      @media (max-width: 768px) {
        .paycan-price-row {
          grid-template-columns: 1fr;
          gap: 0.5rem;
          text-align: left;
        }

        .paycan-price-name,
        .paycan-price-amount {
          font-size: 0.875rem;
        }

        .paycan-price-row .paycan-btn {
          width: 100%;
          min-width: unset;
        }
      }
    `;
  }
}
