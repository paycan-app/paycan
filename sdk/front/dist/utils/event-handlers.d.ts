/**
 * Shared event handler utilities for PayCan SDK components
 * This module provides reusable event handling logic
 */
export declare class EventHandlers {
    /**
     * Create an escape key handler for modal closing
     */
    static createEscapeHandler(closeCallback: () => void): (e: KeyboardEvent) => void;
    /**
     * Create a pagination handler
     */
    static createPaginationHandler(loadCallback: (page: number) => Promise<void>): (e: Event) => Promise<void>;
    /**
     * Create a debounced function
     */
    static debounce<T extends (...args: any[]) => any>(func: T, wait: number): (...args: Parameters<T>) => void;
    /**
     * Create a throttled function
     */
    static throttle<T extends (...args: any[]) => any>(func: T, limit: number): (...args: Parameters<T>) => void;
}
//# sourceMappingURL=event-handlers.d.ts.map