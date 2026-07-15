/**
 * Shared modal utility functions for PayCan SDK components
 * This module provides common modal functionality
 */

export class ModalHelpers {
  /**
   * Check if dark mode is enabled
   */
  static isDarkMode(): boolean {
    if (typeof window === 'undefined') return false;
    
    // Check for explicit theme setting
    const theme = document.documentElement.getAttribute('data-theme') || 
                  document.body.getAttribute('data-theme');
    if (theme === 'dark') return true;
    if (theme === 'light') return false;
    
    // Check for dark mode class
    if (document.documentElement.classList.contains('dark') || 
        document.body.classList.contains('dark')) {
      return true;
    }
    
    // Check system preference
    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
  }

  /**
   * Generate loading state HTML
   */
  static getLoadingState(): string {
    return `
      <div class="paycan-loading">
        <div class="paycan-spinner"></div>
        <p>Loading...</p>
      </div>
    `;
  }

  /**
   * Generate empty state HTML
   */
  static getEmptyState(message: string, icon?: string): string {
    return `
      <div class="paycan-empty-state">
        ${icon ? `<div class="paycan-empty-icon">${icon}</div>` : ''}
        <p class="paycan-empty-message">${message}</p>
      </div>
    `;
  }

  /**
   * Generate pagination HTML
   */
  static getPagination(currentPage: number, totalPages: number): string {
    if (totalPages <= 1) return '';

    let pagination = '<div class="paycan-pagination">';
    
    // Previous button
    if (currentPage > 1) {
      pagination += `<button class="paycan-pagination-btn" data-page="${currentPage - 1}">Previous</button>`;
    }
    
    // Page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    if (startPage > 1) {
      pagination += `<button class="paycan-pagination-btn" data-page="1">1</button>`;
      if (startPage > 2) {
        pagination += '<span class="paycan-pagination-ellipsis">...</span>';
      }
    }
    
    for (let i = startPage; i <= endPage; i++) {
      const isActive = i === currentPage ? ' paycan-pagination-active' : '';
      pagination += `<button class="paycan-pagination-btn${isActive}" data-page="${i}">${i}</button>`;
    }
    
    if (endPage < totalPages) {
      if (endPage < totalPages - 1) {
        pagination += '<span class="paycan-pagination-ellipsis">...</span>';
      }
      pagination += `<button class="paycan-pagination-btn" data-page="${totalPages}">${totalPages}</button>`;
    }
    
    // Next button
    if (currentPage < totalPages) {
      pagination += `<button class="paycan-pagination-btn" data-page="${currentPage + 1}">Next</button>`;
    }
    
    pagination += '</div>';
    return pagination;
  }

  /**
   * Create modal container with shadow DOM
   */
  static createModalContainer(id: string): { container: HTMLElement; shadowRoot: ShadowRoot } {
    const container = document.createElement('div');
    container.id = id;
    container.style.cssText = `
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 999999;
      pointer-events: none;
    `;
    
    const shadowRoot = container.attachShadow({ mode: 'closed' });
    document.body.appendChild(container);
    
    return { container, shadowRoot };
  }

  /**
   * Remove modal container
   */
  static removeModalContainer(container: HTMLElement | null): void {
    if (container && container.parentNode) {
      container.parentNode.removeChild(container);
    }
  }
}