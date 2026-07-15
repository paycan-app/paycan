/**
 * HTTP Client with automatic token management
 */
import type { PayCanConfig } from './types';
export declare class HttpClient {
    private config;
    private token;
    private tokenExpiry;
    private refreshPromise;
    private readonly TOKEN_KEY;
    constructor(config: PayCanConfig);
    /**
     * Load token from localStorage on initialization
     */
    private loadTokenFromStorage;
    /**
     * Save token to localStorage
     */
    private saveTokenToStorage;
    /**
     * Remove token from localStorage
     */
    private removeTokenFromStorage;
    /**
     * Set the authentication token
     */
    setToken(token: string): void;
    /**
     * Get current token
     */
    getToken(): string | null;
    /**
     * Clear the authentication token
     */
    clearToken(): void;
    /**
     * Check if token needs refresh
     */
    private needsRefresh;
    /**
     * Parse JWT token to get expiry time
     * Note: Sanctum tokens are not JWTs and don't contain expiry info
     */
    private parseTokenExpiry;
    /**
     * Make HTTP request with automatic token refresh
     */
    request<T>(endpoint: string, options?: RequestInit, skipAuthCheck?: boolean): Promise<T>;
    /**
     * Ensure we have a valid token, refresh if needed
     */
    private ensureValidToken;
    /**
     * Refresh the authentication token
     * This is called automatically when token is close to expiry
     */
    refreshToken(): Promise<void>;
    /**
     * Handle HTTP response
     */
    private handleResponse;
    /**
     * Debug logging
     */
    private log;
    /**
     * GET request
     */
    get<T>(endpoint: string, params?: Record<string, any>, skipAuthCheck?: boolean): Promise<T>;
    /**
     * POST request
     */
    post<T>(endpoint: string, data?: any, skipAuthCheck?: boolean): Promise<T>;
    /**
     * PUT request
     */
    put<T>(endpoint: string, data?: any): Promise<T>;
    /**
     * DELETE request
     */
    delete<T>(endpoint: string): Promise<T>;
    private isExpired;
}
//# sourceMappingURL=http-client.d.ts.map