/**
 * Shared event handler utilities for PayCan SDK components
 * This module provides reusable event handling logic
 */

export class EventHandlers {
  /**
   * Create an escape key handler for modal closing
   */
  static createEscapeHandler(closeCallback: () => void) {
    return (e: KeyboardEvent) => {
      if (e.key === 'Escape') {
        closeCallback();
      }
    };
  }

  /**
   * Create a pagination handler
   */
  static createPaginationHandler(loadCallback: (page: number) => Promise<void>) {
    return async (e: Event) => {
      const target = e.target as HTMLElement;
      const page = target.getAttribute('data-page');
      if (page) {
        await loadCallback(parseInt(page, 10));
      }
    };
  }

  /**
   * Create a debounced function
   */
  static debounce<T extends (...args: any[]) => any>(
    func: T,
    wait: number
  ): (...args: Parameters<T>) => void {
    let timeout: NodeJS.Timeout;
    return (...args: Parameters<T>) => {
      clearTimeout(timeout);
      timeout = setTimeout(() => func(...args), wait);
    };
  }

  /**
   * Create a throttled function
   */
  static throttle<T extends (...args: any[]) => any>(
    func: T,
    limit: number
  ): (...args: Parameters<T>) => void {
    let inThrottle: boolean;
    return (...args: Parameters<T>) => {
      if (!inThrottle) {
        func(...args);
        inThrottle = true;
        setTimeout(() => inThrottle = false, limit);
      }
    };
  }
}