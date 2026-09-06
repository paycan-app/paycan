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
  private activeTab: 'all' | 'usages' = 'all';
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

      // 2. Fetch transactions based on active tab
      const txRes =
        this.activeTab === 'usages'
          ? await this.sdk.wallets.usages({ page, per_page: 10 })
          : await this.sdk.wallets.transactions({ page, per_page: 10 });

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
          <h2 class="paycan-modal-title">Credit Wallets & Usage</h2>
          <p class="paycan-modal-subtitle">Manage AI credits, track token deductions, and view usage logs.</p>
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
    return `
      <!-- Wallet Balances Cards -->
      <div class="paycan-wallets-grid">
        ${this.renderWalletCard('basic', 'Basic Credits', 'Standard models & daily tasks', 'blue')}
        ${this.renderWalletCard('premium', 'Premium Credits', 'Frontier models & high reasoning', 'purple')}
      </div>

      <!-- Top-up Banner -->
      <div class="paycan-topup-banner">
        <div>
          <div class="paycan-banner-title">Need additional credits?</div>
          <div class="paycan-banner-desc">Top up your balance instantly or upgrade your subscription plan.</div>
        </div>
        <button class="paycan-btn paycan-btn-primary paycan-btn-sm paycan-topup-btn">
          Get More Credits
        </button>
      </div>

      <!-- Navigation Tabs -->
      <div class="paycan-tabs">
        <button class="paycan-tab ${this.activeTab === 'all' ? 'active' : ''}" data-tab="all">
          All Activity
        </button>
        <button class="paycan-tab ${this.activeTab === 'usages' ? 'active' : ''}" data-tab="usages">
          AI Usage Logs
        </button>
      </div>

      <!-- Table Section -->
      ${this.loading ? this.getLoadingState() : this.renderTransactionsTable()}
    `;
  }

  private renderWalletCard(type: string, title: string, subtitle: string, color: 'blue' | 'purple'): string {
    const wallet = this.wallets.find((w) => w.type === type);
    const balance = wallet ? Number(wallet.balance) : 0;
    const formattedBalance = new Intl.NumberFormat('en-US', {
      minimumFractionDigits: 0,
      maximumFractionDigits: 2,
    }).format(balance);

    return `
      <div class="paycan-wallet-card paycan-wallet-${color}">
        <div class="paycan-card-header">
          <div class="paycan-card-badge">${type === 'premium' ? '⚡ High Priority' : '✓ Active'}</div>
          <div class="paycan-card-type">${title}</div>
        </div>
        <div class="paycan-card-balance">
          <span class="paycan-balance-num">${formattedBalance}</span>
          <span class="paycan-balance-unit">credits</span>
        </div>
        <div class="paycan-card-desc">${subtitle}</div>
      </div>
    `;
  }

  private renderTransactionsTable(): string {
    if (this.transactions.length === 0) {
      return `
        <div class="paycan-empty-state">
          <div class="paycan-empty-icon">📊</div>
          <div class="paycan-empty-title">No credit activity found</div>
          <div class="paycan-empty-description">
            ${this.activeTab === 'usages' ? 'No AI agent usages recorded yet.' : 'No transactions recorded yet.'}
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

    let metaSnippet = '';
    if (tx.meta && typeof tx.meta === 'object') {
      const parts = [];
      if (tx.meta.model) parts.push(tx.meta.model);
      if (tx.meta.tokens) parts.push(`${tx.meta.tokens} tokens`);
      if (parts.length > 0) {
        metaSnippet = `<span class="paycan-meta-badge">${parts.join(' • ')}</span>`;
      }
    }

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
            ${sign} ${actionLabel}
          </span>
        </td>
        <td>
          <div class="paycan-desc-cell">
            <span class="paycan-desc-text">${tx.description || 'Usage transaction'}</span>
            ${refSnippet}
            ${metaSnippet}
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

    // Tab buttons
    const tabs = this.shadowRoot.querySelectorAll('.paycan-tab');
    tabs.forEach((tab) => {
      tab.addEventListener('click', async (e) => {
        const target = e.currentTarget as HTMLElement;
        const newTab = target.getAttribute('data-tab') as 'all' | 'usages';
        if (newTab !== this.activeTab) {
          this.activeTab = newTab;
          this.currentPage = 1;
          await this.loadData(1);
          this.refreshModal();
        }
      });
    });

    // Top-up button
    const topupBtn = this.shadowRoot.querySelector('.paycan-topup-btn');
    if (topupBtn) {
      topupBtn.addEventListener('click', () => {
        if (this.options.onTopup) {
          this.options.onTopup();
        } else if (this.options.onTopupRequested) {
          this.options.onTopupRequested(this.wallets[0]);
        } else if (typeof (this.sdk as any).openProductsModal === 'function') {
          this.close();
          (this.sdk as any).openProductsModal({ type: 'subscription' });
        }
      });
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
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
      }

      .paycan-theme-dark .paycan-wallet-card {
        background: #1e293b;
        border-color: #334155;
      }

      .paycan-wallet-blue {
        border-color: rgba(59, 130, 246, 0.3);
        background: linear-gradient(145deg, #ffffff 0%, #eff6ff 100%);
      }

      .paycan-theme-dark .paycan-wallet-blue {
        border-color: rgba(59, 130, 246, 0.3);
        background: linear-gradient(145deg, #0f172a 0%, #1e293b 100%);
      }

      .paycan-wallet-purple {
        border-color: rgba(168, 85, 247, 0.3);
        background: linear-gradient(145deg, #ffffff 0%, #faf5ff 100%);
      }

      .paycan-theme-dark .paycan-wallet-purple {
        border-color: rgba(168, 85, 247, 0.3);
        background: linear-gradient(145deg, #0f172a 0%, #2e1065 100%);
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

      .paycan-card-badge {
        font-size: 0.6875rem;
        font-weight: 600;
        padding: 0.125rem 0.5rem;
        border-radius: 9999px;
        background: rgba(0, 0, 0, 0.05);
        color: #475569;
      }

      .paycan-theme-dark .paycan-card-badge {
        background: rgba(255, 255, 255, 0.1);
        color: #cbd5e1;
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

      .paycan-card-desc {
        font-size: 0.75rem;
        color: #64748b;
        line-height: 1.4;
      }

      .paycan-theme-dark .paycan-card-desc {
        color: #94a3b8;
      }

      /* Topup Banner */
      .paycan-topup-banner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.875rem 1.25rem;
        border-radius: 10px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        margin-bottom: 1.25rem;
      }

      .paycan-theme-dark .paycan-topup-banner {
        background: #0f172a;
        border-color: #334155;
      }

      .paycan-banner-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: #0f172a;
      }

      .paycan-theme-dark .paycan-banner-title {
        color: #f8fafc;
      }

      .paycan-banner-desc {
        font-size: 0.75rem;
        color: #64748b;
      }

      .paycan-theme-dark .paycan-banner-desc {
        color: #94a3b8;
      }

      /* Tabs */
      .paycan-tabs {
        display: flex;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 1rem;
        gap: 0.5rem;
      }

      .paycan-theme-dark .paycan-tabs {
        border-color: #334155;
      }

      .paycan-tab {
        background: transparent;
        border: none;
        padding: 0.625rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #64748b;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        transition: all 0.2s;
      }

      .paycan-theme-dark .paycan-tab {
        color: #94a3b8;
      }

      .paycan-tab:hover {
        color: #0f172a;
      }

      .paycan-theme-dark .paycan-tab:hover {
        color: #ffffff;
      }

      .paycan-tab.active {
        color: #3b82f6;
        border-bottom-color: #3b82f6;
        font-weight: 600;
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

      .paycan-meta-badge {
        font-size: 0.6875rem;
        padding: 0.125rem 0.375rem;
        border-radius: 4px;
        background: #f1f5f9;
        color: #475569;
        display: inline-block;
        width: fit-content;
      }

      .paycan-theme-dark .paycan-meta-badge {
        background: #334155;
        color: #cbd5e1;
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
