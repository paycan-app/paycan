/**
 * PayCan Wallets & Credit Usage Modal Web Component
 *
 * A framework-agnostic modal displaying user credit wallets, balances,
 * transaction history, and AI agent usage logs.
 */

import type { PayCan } from '../paycan';
import type { Wallet, WalletTransaction } from '../types';
import { getAllSharedStyles, ToastHelper } from './shared-styles';

export interface WalletsModalOptions {
  theme?: 'light' | 'dark' | 'auto';
  onClose?: () => void;
  onError?: (error: Error) => void;
  onTopup?: () => void;
  onTopupRequested?: (wallet?: Wallet) => void;
}

export class WalletsModal {
  private sdk: PayCan;
  private options: WalletsModalOptions;
  private container: HTMLElement | null = null;
  private shadowRoot: ShadowRoot | null = null;
  private overlay: HTMLElement | null = null;
  private modal: HTMLElement | null = null;
  private wallets: Wallet[] = [];
  private transactions: WalletTransaction[] = [];
  private loading: boolean = false;
  private currentPage: number = 1;
  private totalPages: number = 1;

  constructor(sdk: PayCan, options: WalletsModalOptions = {}) {
    this.sdk = sdk;
    this.options = options;
  }

  /**
   * Open the modal and load wallet balances & history
   */
  async open(): Promise<void> {
    try {
      this.loading = true;
      this.createModal();
      await this.loadData();
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
   * Load wallet balances and transactions from API
   */
  private async loadData(page: number = 1): Promise<void> {
    this.loading = true;
    try {
      // 1. Fetch user wallets
      const walletRes = await this.sdk.wallets.list();
      this.wallets = walletRes.data || [];

      // 2. Fetch all activity transactions
      const txRes = await this.sdk.wallets.transactions({ page, per_page: 10 });

      this.transactions = txRes.data || [];
      this.currentPage = txRes.meta?.current_page || 1;
      this.totalPages = txRes.meta?.last_page || 1;
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

    this.container = document.createElement('div');
    this.container.setAttribute('id', 'paycan-wallets-modal');

    this.shadowRoot = this.container.attachShadow({ mode: 'open' });

    const styleEl = document.createElement('style');
    styleEl.textContent = this.getStyles();
    this.shadowRoot.appendChild(styleEl);

    this.overlay = document.createElement('div');
    this.overlay.className = `paycan-modal-overlay paycan-show ${themeClass}`;

    this.modal = document.createElement('div');
    this.modal.className = `paycan-modal paycan-modal-wide paycan-show ${themeClass}`;
    this.modal.innerHTML = this.getModalContent();

    this.overlay.appendChild(this.modal);
    this.shadowRoot.appendChild(this.overlay);

    document.body.appendChild(this.container);

    this.attachEventListeners();

    this.overlay.addEventListener('click', (e) => {
      if (e.target === this.overlay) {
        this.close();
      }
    });

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

  private handleEscapeKey = (e: KeyboardEvent): void => {
    if (e.key === 'Escape') {
      this.close();
    }
  };

  private isDarkMode(): boolean {
    const theme = this.options.theme || 'auto';
    if (theme === 'dark') return true;
    if (theme === 'light') return false;
    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
  }

  private getModalContent(): string {
    return `
      <div class="paycan-modal-header">
        <div class="paycan-header-content">
          <h2 class="paycan-modal-title">Wallets & Credits</h2>
          <p class="paycan-modal-subtitle">View wallet balances, transactions, and usage history.</p>
        </div>
        <button class="paycan-close-btn" aria-label="Close">×</button>
      </div>

      <div class="paycan-toast">
        <div class="paycan-toast-content"></div>
      </div>

      <div class="paycan-modal-body">
        ${this.loading && this.wallets.length === 0 ? this.getLoadingState() : this.renderBody()}
      </div>

      ${this.renderFooter()}
    `;
  }

  private renderBody(): string {
    const walletsToRender =
      this.wallets.length > 0
        ? this.wallets
        : [
            { type: 'basic', balance: 0, currency: 'credits' } as Wallet,
            { type: 'premium', balance: 0, currency: 'credits' } as Wallet,
          ];

    return `
      <!-- Wallet Balances Cards -->
      <div class="paycan-wallets-grid">
        ${walletsToRender.map((w) => this.renderWalletCard(w)).join('')}
      </div>

      <!-- Activity Table Section -->
      <div class="paycan-section-header">
        <h3 class="paycan-section-title">Activity</h3>
      </div>

      <!-- Table Section -->
      ${this.loading ? this.getLoadingState() : this.renderTransactionsTable()}
    `;
  }

  private renderWalletCard(wallet: Wallet): string {
    const balance = Number(wallet.balance) || 0;
    const formattedBalance = new Intl.NumberFormat('en-US', {
      minimumFractionDigits: 0,
      maximumFractionDigits: 2,
    }).format(balance);
    const title = (wallet.type || 'basic').charAt(0).toUpperCase() + (wallet.type || 'basic').slice(1) + ' Wallet';
    const currency = wallet.currency || 'credits';

    return `
      <div class="paycan-wallet-card">
        <div class="paycan-card-header">
          <div class="paycan-card-type">${title}</div>
        </div>
        <div class="paycan-card-balance">
          <span class="paycan-balance-num">${formattedBalance}</span>
          <span class="paycan-balance-unit">${currency}</span>
        </div>
      </div>
    `;
  }

  private renderTransactionsTable(): string {
    if (this.transactions.length === 0) {
      return `
        <div class="paycan-empty-state">
          <div class="paycan-empty-icon">📊</div>
          <div class="paycan-empty-title">No activity found</div>
          <div class="paycan-empty-description">
            No transactions recorded yet.
          </div>
        </div>
      `;
    }

    return `
      <div class="paycan-table-container">
        <table class="paycan-table">
          <thead>
            <tr>
              <th>Type</th>
              <th>Description</th>
              <th>Date</th>
              <th style="text-align: right;">Amount</th>
              <th style="text-align: right;">Balance</th>
            </tr>
          </thead>
          <tbody>
            ${this.transactions.map((tx) => this.renderTransactionRow(tx)).join('')}
          </tbody>
        </table>
      </div>
    `;
  }

  private renderTransactionRow(tx: WalletTransaction): string {
    const isCredit = tx.type === 'credit';
    const sign = isCredit ? '+' : '-';
    const amountClass = isCredit ? 'text-success' : 'text-debit';
    const formattedAmount = Number(tx.amount).toFixed(2);
    const formattedBalance = Number(tx.balance_after).toFixed(2);
    const actionLabel = tx.action ? tx.action.replace(/_/g, ' ') : 'transaction';

    const refSnippet = tx.reference_id
      ? `<span class="paycan-ref-tag" title="Reference ID">#${tx.reference_id}</span>`
      : '';

    const formattedDate = new Date(tx.created_at).toLocaleDateString('en-US', {
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });

    return `
      <tr>
        <td>
          <span class="paycan-badge ${isCredit ? 'badge-credit' : 'badge-debit'}">
            ${actionLabel}
          </span>
        </td>
        <td>
          <div class="paycan-desc-cell">
            <span class="paycan-desc-text">${tx.description || 'Transaction'}</span>
            ${refSnippet}
          </div>
        </td>
        <td class="paycan-date-cell">${formattedDate}</td>
        <td style="text-align: right;" class="paycan-amount-cell ${amountClass}">
          ${sign}${formattedAmount}
        </td>
        <td style="text-align: right;" class="paycan-balance-cell">
          ${formattedBalance}
        </td>
      </tr>
    `;
  }

  private renderFooter(): string {
    if (this.totalPages <= 1) {
      return '';
    }

    return `
      <div class="paycan-modal-footer">
        <div class="paycan-pagination">
          <button class="paycan-btn paycan-btn-secondary paycan-btn-sm prev-page" ${this.currentPage <= 1 ? 'disabled' : ''}>
            Previous
          </button>
          <span class="paycan-page-info">Page ${this.currentPage} of ${this.totalPages}</span>
          <button class="paycan-btn paycan-btn-secondary paycan-btn-sm next-page" ${this.currentPage >= this.totalPages ? 'disabled' : ''}>
            Next
          </button>
        </div>
      </div>
    `;
  }

  private getLoadingState(): string {
    return `
      <div class="paycan-loading-container">
        <div class="paycan-spinner"></div>
        <div class="paycan-loading-text">Loading wallet data...</div>
      </div>
    `;
  }

  private attachEventListeners(): void {
    if (!this.shadowRoot) return;

    // Close button
    const closeBtn = this.shadowRoot.querySelector('.paycan-close-btn');
    if (closeBtn) {
      closeBtn.addEventListener('click', () => this.close());
    }

    // Pagination buttons
    const prevBtn = this.shadowRoot.querySelector('.prev-page');
    if (prevBtn) {
      prevBtn.addEventListener('click', async () => {
        if (this.currentPage > 1) {
          await this.loadData(this.currentPage - 1);
          this.refreshModal();
        }
      });
    }

    const nextBtn = this.shadowRoot.querySelector('.next-page');
    if (nextBtn) {
      nextBtn.addEventListener('click', async () => {
        if (this.currentPage < this.totalPages) {
          await this.loadData(this.currentPage + 1);
          this.refreshModal();
        }
      });
    }
  }

  private handleError(error: Error): void {
    if (this.modal) {
      ToastHelper.showToast(this.modal, error.message || 'Failed to load credit wallet details.', 'error');
    }
    if (this.options.onError) {
      this.options.onError(error);
    }
  }

  private getStyles(): string {
    return `
      ${getAllSharedStyles()}

      .paycan-modal-wide {
        max-width: 780px;
        width: 100%;
      }

      .paycan-header-content {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
      }

      .paycan-modal-subtitle {
        margin: 0;
        font-size: 0.8125rem;
        color: #64748b;
      }

      .paycan-theme-dark .paycan-modal-subtitle {
        color: #94a3b8;
      }

      /* Wallets Cards Grid */
      .paycan-wallets-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1.25rem;
      }

      @media (max-width: 640px) {
        .paycan-wallets-grid {
          grid-template-columns: 1fr;
        }
      }

      .paycan-wallet-card {
        padding: 1.25rem;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
      }

      .paycan-theme-dark .paycan-wallet-card {
        background: #1e293b;
        border-color: #334155;
      }

      .paycan-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
      }

      .paycan-card-type {
        font-size: 0.9375rem;
        font-weight: 600;
        color: #0f172a;
      }

      .paycan-theme-dark .paycan-card-type {
        color: #f8fafc;
      }

      .paycan-card-balance {
        display: flex;
        align-items: baseline;
        gap: 0.375rem;
      }

      .paycan-balance-num {
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: -0.025em;
        color: #0f172a;
      }

      .paycan-theme-dark .paycan-balance-num {
        color: #ffffff;
      }

      .paycan-balance-unit {
        font-size: 0.8125rem;
        font-weight: 500;
        color: #64748b;
      }

      /* Activity Section Header */
      .paycan-section-header {
        margin-bottom: 0.75rem;
      }

      .paycan-section-title {
        margin: 0;
        font-size: 0.9375rem;
        font-weight: 600;
        color: #0f172a;
      }

      .paycan-theme-dark .paycan-section-title {
        color: #f8fafc;
      }

      /* Table Styles */
      .paycan-table-container {
        overflow-x: auto;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
      }

      .paycan-theme-dark .paycan-table-container {
        border-color: #334155;
      }

      .paycan-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 0.8125rem;
      }

      .paycan-table th {
        padding: 0.625rem 0.875rem;
        background: #f8fafc;
        color: #64748b;
        font-weight: 600;
        font-size: 0.6875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #e2e8f0;
      }

      .paycan-theme-dark .paycan-table th {
        background: #0f172a;
        color: #94a3b8;
        border-color: #334155;
      }

      .paycan-table td {
        padding: 0.75rem 0.875rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
      }

      .paycan-theme-dark .paycan-table td {
        border-color: #1e293b;
      }

      .paycan-desc-cell {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
      }

      .paycan-desc-text {
        color: #334155;
        font-weight: 500;
      }

      .paycan-theme-dark .paycan-desc-text {
        color: #e2e8f0;
      }

      .paycan-ref-tag {
        font-family: monospace;
        font-size: 0.6875rem;
        color: #64748b;
      }



      .paycan-date-cell {
        color: #64748b;
        font-size: 0.75rem;
        white-space: nowrap;
      }

      .paycan-amount-cell {
        font-weight: 700;
        font-size: 0.875rem;
        white-space: nowrap;
      }

      .text-success {
        color: #16a34a;
      }

      .text-debit {
        color: #dc2626;
      }

      .paycan-theme-dark .text-success {
        color: #4ade80;
      }

      .paycan-theme-dark .text-debit {
        color: #f87171;
      }

      .paycan-balance-cell {
        color: #64748b;
        font-weight: 500;
      }

      .badge-credit {
        background: #dcfce7;
        color: #15803d;
      }

      .badge-debit {
        background: #fee2e2;
        color: #b91c1c;
      }

      .paycan-theme-dark .badge-credit {
        background: #064e3b;
        color: #86efac;
      }

      .paycan-theme-dark .badge-debit {
        background: #7f1d1d;
        color: #fca5a5;
      }

      /* Pagination */
      .paycan-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
      }

      .paycan-page-info {
        font-size: 0.8125rem;
        color: #64748b;
      }
    `;
  }
}
