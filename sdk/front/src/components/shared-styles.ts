/**
 * Shared Styles for PayCan Modals
 *
 * This module contains reusable CSS styles for all PayCan modals
 * to ensure consistency and reduce code duplication.
 */

/**
 * Get base modal styles (overlay, container, header, footer, etc.)
 */
export function getBaseModalStyles(): string {
  return `
    /* PayCan Modal Base Styles */

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
      opacity: 0;
      transition: opacity 0.2s ease-in-out;
    }

    .paycan-modal-overlay.paycan-show {
      opacity: 1;
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
      transform: scale(0.95);
      transition: transform 0.2s ease-in-out;
    }

    .paycan-modal.paycan-show {
      transform: scale(1);
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

    .paycan-modal-header-content {
      display: flex;
      align-items: center;
      justify-content: space-between;
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

    /* Modal Body */
    .paycan-modal-body {
      flex: 1;
      overflow-y: auto;
      padding: 1.5rem;
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
  `;
}

/**
 * Get toast notification styles
 */
export function getToastStyles(): string {
  return `
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
  `;
}

/**
 * Get common button styles
 */
export function getButtonStyles(): string {
  return `
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

    .paycan-btn-lg {
      padding: 0.75rem 1.5rem;
      font-size: 1rem;
      font-weight: 400;
    }

    .paycan-btn-primary {
      background: var(--paycan-accent);
      color: white;
    }

    .paycan-btn-primary:hover:not(:disabled) {
      background: var(--paycan-accent-hover);
    }

    .paycan-btn-primary:active:not(:disabled) {
      transform: scale(0.98);
    }

    .paycan-btn-secondary {
      background: transparent;
      border: 1px solid;
    }

    .paycan-theme-light .paycan-btn-secondary {
      border-color: #d1d5db;
      color: #111827;
    }

    .paycan-theme-dark .paycan-btn-secondary {
      border-color: #4b5563;
      color: #f9fafb;
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

    .paycan-btn-success {
      background: #10b981;
      color: white;
    }

    .paycan-btn-success:hover:not(:disabled) {
      background: #059669;
    }

    .paycan-btn-info {
      background: #3b82f6;
      color: white;
    }

    .paycan-btn-info:hover:not(:disabled) {
      background: #2563eb;
    }

    .paycan-btn-warning {
      background: #f59e0b;
      color: white;
    }

    .paycan-btn-warning:hover:not(:disabled) {
      background: #d97706;
    }

    .paycan-btn-purple {
      background: #8b5cf6;
      color: white;
    }

    .paycan-btn-purple:hover:not(:disabled) {
      background: #7c3aed;
    }

    /* Button Group */
    .paycan-button-group {
      display: flex;
      gap: 0.75rem;
      justify-content: flex-end;
    }

    /* Push Button Animation */
    .paycan-btn-push {
      position: relative;
      transition: all 0.2s ease;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .paycan-btn-push:hover:not(:disabled) {
      transform: translateY(-2px);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .paycan-btn-push:active:not(:disabled) {
      transform: translateY(0);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
  `;
}

/**
 * Get common card styles
 */
export function getCardStyles(): string {
  return `
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
  `;
}

/**
 * Get common badge styles
 */
export function getBadgeStyles(): string {
  return `
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
  `;
}

/**
 * Get loading state styles
 */
export function getLoadingStyles(): string {
  return `
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
  `;
}

/**
 * Get empty state styles
 */
export function getEmptyStateStyles(): string {
  return `
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
  `;
}

/**
 * Get all shared styles combined
 */
export function getAllSharedStyles(): string {
  return [
    getBaseModalStyles(),
    getToastStyles(),
    getButtonStyles(),
    getCardStyles(),
    getBadgeStyles(),
    getLoadingStyles(),
    getEmptyStateStyles(),
  ].join('\n');
}

/**
 * Base toast helper for consistent behavior across all modals
 */
export class ToastHelper {
  /**
   * Show toast notification
   */
  static showToast(
    modal: HTMLElement | null,
    message: string,
    type: 'success' | 'error' | 'info' = 'info'
  ): void {
    const toast = modal?.querySelector('.paycan-toast') as HTMLElement;
    const content = modal?.querySelector('.paycan-toast-content') as HTMLElement;

    if (!toast || !content) return;

    const icon = type === 'success' ? '✓' : type === 'error' ? '✕' : 'ℹ';
    content.innerHTML = `<span>${icon}</span><span>${ToastHelper.escapeHtml(message)}</span>`;

    toast.classList.add('show', `paycan-toast-${type}`);

    setTimeout(() => {
      toast.classList.remove('show', `paycan-toast-${type}`);
    }, 10000); // 10s
  }

  /**
   * Escape HTML to prevent XSS
   */
  private static escapeHtml(text: string): string {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }
}
