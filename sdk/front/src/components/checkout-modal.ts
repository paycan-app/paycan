/**
 * PayCan Checkout Modal Web Component
 *
 * A framework-agnostic checkout modal that can be embedded anywhere
 */

import type { PayCan } from '../paycan';
import type { CheckoutPreviewResponse, CheckoutPreviewPrice } from '../types';
import { getAllSharedStyles, ToastHelper } from './shared-styles';
import { Formatters } from '../utils/formatters';
import { EventHandlers } from '../utils/event-handlers';

export interface CheckoutModalOptions {
  productId?: number | string;
  priceId?: number | string;
  theme?: 'light' | 'dark' | 'auto';
  onSuccess?: (checkoutUrl: string) => void;
  onCancel?: () => void;
  onError?: (error: Error) => void;
}

export class CheckoutModal {
  private sdk: PayCan;
  private options: CheckoutModalOptions;
  private container: HTMLElement | null = null;
  private shadowRoot: ShadowRoot | null = null;
  private overlay: HTMLElement | null = null;
  private modal: HTMLElement | null = null;
  private previewData: CheckoutPreviewResponse | null = null;
  private selectedPriceId: number | string | null = null;
  private selectedGateway: string = 'stripe';
  private loading: boolean = false;

  constructor(sdk: PayCan, options: CheckoutModalOptions) {
    this.sdk = sdk;
    this.options = options;
    this.selectedPriceId = options.priceId || null;
  }

  /**
   * Open the modal and load preview data
   */
  async open(): Promise<void> {
    try {
      let sessionExpired = false;

      if ((this.sdk as any).isAuthenticated && this.sdk.isAuthenticated()) {
        try {
          await (this.sdk as any).me();
        } catch (err: any) {
          if (err?.status === 401 && (this.sdk as any).logout) {
            (this.sdk as any).logout();
            sessionExpired = true;
          }
        }
      }

      // Load preview data
      await this.loadPreview();

      // Create and show modal
      this.createModal();
      this.show();

      // Inform user after modal is visible
      if (sessionExpired) {
        this.showToast('Session expired. Continue as guest or log in.', 'info');
      }
    } catch (error) {
      this.handleError(error as Error);
    }
  }

  /**
   * Close and destroy the modal
   */
  close(): void {
    // Remove the entire shadow DOM container
    if (this.container) {
      this.container.remove();
      this.container = null;
      this.shadowRoot = null;
      this.overlay = null;
      this.modal = null;
    }
    // Remove escape key listener
    document.removeEventListener('keydown', this.handleEscapeKey);

    if (this.options.onCancel) {
      this.options.onCancel();
    }
  }

  /**
   * Load preview data from API
   */
  private async loadPreview(): Promise<void> {
    const params: any = {
      quantity: 1,
    };

    if (this.options.productId) {
      params.product_id = this.options.productId;
      if (this.selectedPriceId) {
        params.selected_price_id = this.selectedPriceId;
      }
    } else if (this.selectedPriceId) {
      params.product_price_id = this.selectedPriceId;
    }

    this.previewData = await this.sdk.checkout.preview(params);

    // Set default selected price if not set
    if (!this.selectedPriceId && this.previewData.selected_price) {
      this.selectedPriceId = this.previewData.selected_price.id;
    }

    // Set default gateway from available payment methods
    if (this.previewData.payment_methods.length > 0) {
      this.selectedGateway = this.previewData.payment_methods[0].key;
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
    this.container.setAttribute('id', 'paycan-checkout-modal');

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
    this.modal.className = `paycan-modal ${themeClass}`;

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
   * Get all modal CSS styles
   */
  private getStyles(): string {
    return `
      /* PayCan Checkout Modal Styles */

      ${getAllSharedStyles()}

      /* Checkout Modal Specific Styles */

      /* Adjust modal max-width for checkout */
      .paycan-modal {
        max-width: 500px;
      }

      /* Product Card */
      .paycan-product-card {
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid;
      }

      .paycan-theme-light .paycan-product-card {
        background: #ffffff;
        border-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-product-card {
        background: #1f2937;
        border-color: #4b5563;
      }

      .paycan-product-title {
        font-size: 1.125rem;
        font-weight: 500;
        margin: 0 0 0.5rem 0;
      }

      .paycan-product-description {
        font-size: 0.875rem;
        margin: 0;
      }

      .paycan-theme-light .paycan-product-description {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-product-description {
        color: #d1d5db;
      }

      /* Form Fields */
      .paycan-form-group {
        margin-bottom: 1.5rem;
      }

      .paycan-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
      }

      .paycan-label-required {
        color: #ef4444;
      }

      .paycan-input {
        width: 100%;
        padding: 0.625rem;
        border: 1px solid;
        border-radius: 6px;
        font-size: 0.875rem;
        box-sizing: border-box;
      }

      .paycan-theme-light .paycan-input {
        border-color: #d1d5db;
        background: #ffffff;
        color: #111827;
      }

      .paycan-theme-dark .paycan-input {
        border-color: #4b5563;
        background: #374151;
        color: #f9fafb;
      }

      .paycan-help-text {
        font-size: 0.75rem;
        margin: 0.375rem 0 0 0;
      }

      .paycan-theme-light .paycan-help-text {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-help-text {
        color: #9ca3af;
      }

      /* Price Selection */
      .paycan-price-options {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
      }

      .paycan-price-option {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        border: 2px solid;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.15s;
      }

      .paycan-theme-light .paycan-price-option {
        border-color: #e5e7eb;
        background: #ffffff;
      }

      .paycan-theme-dark .paycan-price-option {
        border-color: #4b5563;
        background: #374151;
      }

      .paycan-price-option.selected {
        border-color: var(--paycan-accent);
      }

      .paycan-theme-light .paycan-price-option.selected {
        background: var(--paycan-accent-light);
      }

      .paycan-theme-dark .paycan-price-option.selected {
        background: var(--paycan-accent-dark);
      }

      .paycan-price-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
      }

      .paycan-price-option-icon {
        margin-right: 0.75rem;
        flex-shrink: 0;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
      }

      .paycan-theme-light .paycan-price-option-icon {
        background: #e5e7eb;
      }

      .paycan-theme-dark .paycan-price-option-icon {
        background: #4b5563;
      }

      .paycan-price-option.selected .paycan-price-option-icon {
        background: var(--paycan-accent);
      }

      .paycan-price-option-icon svg {
        width: 10px;
        height: 10px;
        opacity: 0;
        transition: opacity 0.15s;
      }

      .paycan-price-option.selected .paycan-price-option-icon svg {
        opacity: 1;
      }

      .paycan-price-details {
        flex: 1;
      }

      .paycan-price-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
      }

      .paycan-price-name {
        font-weight: 400;
        flex: 1;
        font-size: 0.875rem;
      }

      .paycan-price-amount-group {
        text-align: right;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
      }

      .paycan-price-amount {
        font-weight: 400;
        font-size: 1rem;
      }

      .paycan-price-period {
        font-size: 0.75rem;
        white-space: nowrap;
      }

      .paycan-theme-light .paycan-price-period {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-price-period {
        color: #9ca3af;
      }

      /* Dropdown */
      .paycan-select {
        width: 100%;
        padding: 0.625rem;
        border: 1px solid;
        border-radius: 6px;
        font-size: 0.875rem;
        cursor: pointer;
      }

      .paycan-theme-light .paycan-select {
        border-color: #d1d5db;
        background: #ffffff;
        color: #111827;
      }

      .paycan-theme-dark .paycan-select {
        border-color: #4b5563;
        background: #374151;
        color: #f9fafb;
      }

      /* Price Breakdown */
      .paycan-price-breakdown {
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid;
      }

      .paycan-theme-light .paycan-price-breakdown {
        background: #ffffff;
        border-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-price-breakdown {
        background: #1f2937;
        border-color: #4b5563;
      }

      .paycan-breakdown-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
      }

      .paycan-breakdown-label {
        font-size: 0.875rem;
      }

      .paycan-theme-light .paycan-breakdown-label {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-breakdown-label {
        color: #9ca3af;
      }

      .paycan-breakdown-value {
        font-size: 0.875rem;
        font-weight: 500;
      }

      .paycan-breakdown-divider {
        margin: 0.75rem 0;
        padding-top: 0.75rem;
        border-top: 1px solid;
      }

      .paycan-theme-light .paycan-breakdown-divider {
        border-top-color: #e5e7eb;
      }

      .paycan-theme-dark .paycan-breakdown-divider {
        border-top-color: #374151;
      }

      .paycan-breakdown-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
      }

      .paycan-breakdown-total-label {
        font-weight: 400;
      }

      .paycan-breakdown-total-value {
        text-align: right;
      }

      .paycan-breakdown-total-amount {
        font-size: 1.5rem;
        font-weight: 400;
      }

      .paycan-breakdown-total-period {
        font-size: 0.75rem;
      }

      .paycan-theme-light .paycan-breakdown-total-period {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-breakdown-total-period {
        color: #9ca3af;
      }

      /* Payment Methods */
      .paycan-payment-methods {
        display: grid;
        gap: 0.75rem;
      }

      .paycan-payment-methods.grid-1 {
        grid-template-columns: 1fr;
      }

      .paycan-payment-methods.grid-2 {
        grid-template-columns: repeat(2, 1fr);
      }

      .paycan-payment-method {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        border: 2px solid;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.15s;
      }

      .paycan-theme-light .paycan-payment-method {
        border-color: #e5e7eb;
        background: #ffffff;
      }

      .paycan-theme-dark .paycan-payment-method {
        border-color: #4b5563;
        background: #374151;
      }

      .paycan-payment-method.selected {
        border-color: var(--paycan-accent);
      }

      .paycan-theme-light .paycan-payment-method.selected {
        background: var(--paycan-accent-light);
      }

      .paycan-theme-dark .paycan-payment-method.selected {
        background: var(--paycan-accent-dark);
      }

      .paycan-payment-method.full-width {
        grid-column: 1 / -1;
      }

      .paycan-payment-method input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
      }

      .paycan-payment-method-icon {
        margin-right: 0.75rem;
        flex-shrink: 0;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
      }

      .paycan-theme-light .paycan-payment-method-icon {
        background: #e5e7eb;
      }

      .paycan-theme-dark .paycan-payment-method-icon {
        background: #4b5563;
      }

      .paycan-payment-method.selected .paycan-payment-method-icon {
        background: var(--paycan-accent);
      }

      .paycan-payment-method-icon svg {
        width: 10px;
        height: 10px;
        opacity: 0;
        transition: opacity 0.15s;
      }

      .paycan-payment-method.selected .paycan-payment-method-icon svg {
        opacity: 1;
      }

      .paycan-payment-method-info {
        flex: 1;
        min-width: 0;
      }

      .paycan-payment-method-name {
        font-weight: 400;
        margin-bottom: 0.25rem;
        font-size: 0.875rem;
      }

      .paycan-payment-method-description {
        font-size: 0.75rem;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      .paycan-theme-light .paycan-payment-method-description {
        color: #6b7280;
      }

      .paycan-theme-dark .paycan-payment-method-description {
        color: #9ca3af;
      }

      /* Responsive */
      @media (max-width: 640px) {
        .paycan-modal {
          max-width: 100%;
        }

        .paycan-payment-methods.grid-2 {
          grid-template-columns: 1fr;
        }

        .paycan-button-group {
          flex-direction: column-reverse;
        }

        .paycan-btn {
          width: 100%;
        }
      }
    `;
  }

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
      // Auto mode - detect from system preference
      return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    }
  }

  /**
   * Get modal HTML content
   */
  private getModalContent(): string {
    if (!this.previewData) {
      return '<div class="paycan-modal-body" style="padding: 2rem; text-align: center;">Loading...</div>';
    }

    const isAuthenticated = this.sdk.isAuthenticated();

    const product = this.previewData.product;
    const selectedPrice = this.previewData.selected_price;
    const prices = this.previewData.prices;
    const paymentMethods = this.previewData.payment_methods;

    return `
      <!-- Fixed Header -->
      <div class="paycan-modal-header">
        <h2 class="paycan-modal-title">🛒 Checkout</h2>
        <button class="paycan-close-btn" aria-label="Close">×</button>
      </div>

      <!-- Toast Notification -->
      <div class="paycan-toast">
        <div class="paycan-toast-content"></div>
      </div>

      <!-- Scrollable Body -->
      <div class="paycan-modal-body">
        <!-- Product Info -->
        <div class="paycan-product-card">
          <h3 class="paycan-product-title">${Formatters.escapeHtml(product.name)}</h3>
          ${product.description ? `<p class="paycan-product-description">${Formatters.escapeHtml(Formatters.trimWords(product.description, 15))}</p>` : ''}
        </div>

        ${prices.length > 1 ? this.getPriceSelectionField(prices) : ''}

        ${selectedPrice ? this.getPriceBreakdown(selectedPrice) : ''}

        ${!isAuthenticated ? this.getGuestEmailField() : ''}

        ${paymentMethods.length > 0 ? this.getPaymentMethodsField(paymentMethods) : ''}

        <!-- Security Notice -->
        <div class="paycan-product-card">
          <p class="paycan-help-text" style="margin: 0;">
            🔒 Your payment information is secure and encrypted. We never store your card details.
          </p>
        </div>
      </div>

      <!-- Fixed Footer -->
      <div class="paycan-modal-footer">
        <div class="paycan-button-group">
          <button class="paycan-btn paycan-btn-secondary paycan-cancel-btn">
            Cancel
          </button>
          <button class="paycan-btn paycan-btn-primary paycan-btn-push paycan-checkout-btn" ${this.loading ? 'disabled' : ''}>
            ${this.loading ? 'Processing...' : selectedPrice ? `Pay ${Formatters.formatPrice(selectedPrice.final_price, selectedPrice.currency)}` : 'Continue'}
          </button>
        </div>
      </div>

      <style>
        @keyframes slideDown {
          from {
            opacity: 0;
            transform: translateY(-10px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }
      </style>
    `;
  }

  /**
   * Get guest email field HTML
   */
  private getGuestEmailField(): string {
    return `
      <div class="paycan-form-group">
        <label class="paycan-label">
          Email Address <span class="paycan-label-required">*</span>
        </label>
        <input
          type="email"
          class="paycan-input paycan-email-input"
          placeholder="your@email.com"
          required
        />
      </div>
    `;
  }

  /**
   * Get price selection field HTML
   */
  private getPriceSelectionField(prices: CheckoutPreviewPrice[]): string {
    if (prices.length < 3) {
      // Radio buttons
      return `
        <div class="paycan-form-group">
          <label class="paycan-label">Select Plan</label>
          <div class="paycan-price-options">
            ${prices.map(price => `
              <label class="paycan-price-option ${this.selectedPriceId === price.id ? 'selected' : ''}">
                <input
                  type="radio"
                  name="price"
                  value="${price.id}"
                  class="paycan-price-radio"
                  ${this.selectedPriceId === price.id ? 'checked' : ''}
                />
                <div class="paycan-price-option-icon">
                  <svg viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 3L4.5 8.5L2 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </div>
                <div class="paycan-price-details">
                  <div class="paycan-price-header">
                    <span class="paycan-price-name">${Formatters.escapeHtml(price.name)}</span>
                    <div class="paycan-price-amount-group">
                      <span class="paycan-price-amount">${Formatters.formatPrice(price.final_price, price.currency)}</span>
                      <span class="paycan-price-period">${Formatters.getBillingPeriodText(price.billing_period)}${price.trial_days ? ` • ${price.trial_days} days trial` : ''}</span>
                    </div>
                  </div>
                </div>
              </label>
            `).join('')}
          </div>
        </div>
      `;
    } else {
      // Dropdown
      return `
        <div class="paycan-form-group">
          <label class="paycan-label">Select Plan</label>
          <select class="paycan-select paycan-price-select">
            ${prices.map(price => `
              <option value="${price.id}" ${this.selectedPriceId === price.id ? 'selected' : ''}>
                ${Formatters.escapeHtml(price.name)} - ${Formatters.formatPrice(price.final_price, price.currency)} ${Formatters.getBillingPeriodText(price.billing_period)}
              </option>
            `).join('')}
          </select>
        </div>
      `;
    }
  }

  /**
   * Get price breakdown HTML
   */
  private getPriceBreakdown(price: CheckoutPreviewPrice): string {
    return `
      <div class="paycan-price-breakdown">
        <div class="paycan-breakdown-row">
          <span class="paycan-breakdown-label">Subtotal</span>
          <span class="paycan-breakdown-value">${Formatters.formatPrice(price.subtotal, price.currency)}</span>
        </div>
        <div class="paycan-breakdown-divider"></div>
        <div class="paycan-breakdown-total">
          <span class="paycan-breakdown-total-label">Total</span>
          <div class="paycan-breakdown-total-value">
            <div class="paycan-breakdown-total-amount">${Formatters.formatPrice(price.final_price, price.currency)}</div>
            <div class="paycan-breakdown-total-period">${Formatters.getBillingPeriodText(price.billing_period)}</div>
          </div>
        </div>
      </div>
    `;
  }

  /**
   * Get payment methods field HTML
   */
  private getPaymentMethodsField(methods: any[]): string {
    // Determine grid layout based on number of methods
    // 1 method: 1 per row (full width)
    // 2+ methods: 2 per row (with first item full-width for odd numbers > 1)
    const gridClass = methods.length === 1 ? 'grid-1' : 'grid-2';

    return `
      <div class="paycan-form-group">
        <label class="paycan-label">Payment Method</label>
        <div class="paycan-payment-methods ${gridClass}">
          ${methods.map((method, index) => {
            // For odd numbers > 1, first item spans full width
            const isOdd = methods.length % 2 !== 0 && methods.length > 1;
            const isFirstOfOdd = isOdd && index === 0;
            const fullWidthClass = isFirstOfOdd ? 'full-width' : '';
            const selectedClass = this.selectedGateway === method.key ? 'selected' : '';

            return `
              <label class="paycan-payment-method ${selectedClass} ${fullWidthClass}">
                <input
                  type="radio"
                  name="gateway"
                  value="${method.key}"
                  class="paycan-gateway-radio"
                  ${this.selectedGateway === method.key ? 'checked' : ''}
                />
                <div class="paycan-payment-method-icon">
                  <svg viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 3L4.5 8.5L2 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </div>
                <div class="paycan-payment-method-info">
                  <div class="paycan-payment-method-name">${Formatters.escapeHtml(method.name)}</div>
                  ${method.description ? `<div class="paycan-payment-method-description">${Formatters.escapeHtml(method.description)}</div>` : ''}
                </div>
              </label>
            `;
          }).join('')}
        </div>
      </div>
    `;
  }

  /**
   * Attach event listeners to modal elements
   */
  private attachEventListeners(): void {
    if (!this.modal) return;

    // Close button
    const closeBtn = this.modal.querySelector('.paycan-close-btn');
    closeBtn?.addEventListener('click', () => this.close());

    // Cancel button
    const cancelBtn = this.modal.querySelector('.paycan-cancel-btn');
    cancelBtn?.addEventListener('click', () => this.close());

    // Checkout button
    const checkoutBtn = this.modal.querySelector('.paycan-checkout-btn');
    checkoutBtn?.addEventListener('click', () => this.handleCheckout());

    // Price radio buttons
    const priceRadios = this.modal.querySelectorAll('.paycan-price-radio');
    priceRadios.forEach(radio => {
      radio.addEventListener('change', (e) => {
        const target = e.target as HTMLInputElement;
        this.handlePriceChange(target.value);
      });
    });

    // Price dropdown
    const priceSelect = this.modal.querySelector('.paycan-price-select') as HTMLSelectElement;
    priceSelect?.addEventListener('change', (e) => {
      const target = e.target as HTMLSelectElement;
      this.handlePriceChange(target.value);
    });

    // Gateway radio buttons
    const gatewayRadios = this.modal.querySelectorAll('.paycan-gateway-radio');
    gatewayRadios.forEach(radio => {
      radio.addEventListener('change', (e) => {
        const target = e.target as HTMLInputElement;
        this.selectedGateway = target.value;
        // Update UI to reflect selection
        this.modal?.querySelectorAll('.paycan-payment-method').forEach(method => {
          method.classList.remove('selected');
        });
        target.closest('.paycan-payment-method')?.classList.add('selected');
      });
    });
  }

  /**
   * Handle price change
   */
  private async handlePriceChange(priceId: number | string): Promise<void> {
    this.selectedPriceId = priceId;
    try {
      // Reload preview with new price
      await this.loadPreview();
      // Re-render modal
      if (this.modal) {
        this.modal.innerHTML = this.getModalContent();
        this.attachEventListeners();
      }
    } catch (error) {
      this.handleError(error as Error);
    }
  }

  /**
   * Handle checkout button click
   */
  private async handleCheckout(): Promise<void> {
    if (this.loading) return;

    try {
      this.loading = true;
      this.updateCheckoutButton();

      // Validate email if guest
      let billingEmail: string | undefined;
      if (!this.sdk.isAuthenticated()) {
        const emailInput = this.modal?.querySelector('.paycan-email-input') as HTMLInputElement;
        if (!emailInput || !emailInput.value || !Formatters.isValidEmail(emailInput.value)) {
          throw new Error('Please enter a valid email address');
        }
        billingEmail = emailInput.value;
      }

      // Show processing message
      this.showToast('Creating checkout session...', 'info');

      // Create checkout session
      const checkoutData: any = {
        product_price_id: this.selectedPriceId!,
        gateway: this.selectedGateway,
        quantity: 1,
      };

      if (this.previewData) {
        checkoutData.product_id = this.previewData.product.id;
      }

      if (billingEmail) {
        checkoutData.billing_email = billingEmail;
      }

      const session = await this.sdk.checkout.create(checkoutData);

      // Show redirecting message
      this.showToast('Redirecting to gateway...', 'success');
      await new Promise(resolve => setTimeout(resolve, 1000));

      if (this.options.onSuccess) {
        this.options.onSuccess(session.checkout_url);
      } else {
        window.location.href = session.checkout_url;
      }

      this.close();
    } catch (error) {
      this.loading = false;
      this.updateCheckoutButton();

      const status = (error as any)?.status ?? (error as any)?.statusCode;
      if (status === 401 && (this.sdk as any).logout) {
        // Force logout and re-render into guest mode
        (this.sdk as any).logout();
        this.showToast('Session expired. Continue as guest or log in.', 'info');

        // Re-render modal content so guest email field is shown
        if (this.modal) {
          this.modal.innerHTML = this.getModalContent();
          this.attachEventListeners();
        }
        return;
      }

      this.handleError(error as Error);
    }
  }

  /**
   * Update checkout button state
   */
  private updateCheckoutButton(): void {
    const btn = this.modal?.querySelector('.paycan-checkout-btn') as HTMLButtonElement;
    if (btn) {
      btn.disabled = this.loading;
      btn.textContent = this.loading ? 'Processing...' :
        this.previewData?.selected_price ?
          `Pay ${Formatters.formatPrice(this.previewData.selected_price.final_price, this.previewData.selected_price.currency)}` :
          'Continue';
    }
  }

  /**
   * Handle escape key press
   */
  private handleEscapeKey = EventHandlers.createEscapeHandler(() => this.close());

  /**
   * Show modal with animation
   */
  private show(): void {
    if (this.overlay && this.modal) {
      // Trigger reflow for animation
      this.overlay.offsetHeight;
      this.overlay.classList.add('paycan-show');
      this.modal.classList.add('paycan-show');
    }
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
    console.error('[PayCan Checkout Modal]', error);
    this.showToast(error.message, 'error');
    if (this.options.onError) {
      this.options.onError(error);
    }
  }





  /**
   * Cleanup
   */
  destroy(): void {
    document.removeEventListener('keydown', this.handleEscapeKey);
    this.close();
  }
}
