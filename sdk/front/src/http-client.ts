/**
 * HTTP Client with automatic token management
 */

import type { PayCanConfig, ApiError } from './types';

export class HttpClient {
  private config: PayCanConfig;
  private token: string | null = null;
  private tokenExpiry: number | null = null;
  private refreshPromise: Promise<void> | null = null;
  private readonly TOKEN_KEY = 'paycan_auth_token';

  constructor(config: PayCanConfig) {
    this.config = config;
    this.loadTokenFromStorage();
  }

  /**
   * Load token from localStorage on initialization
   */
  private loadTokenFromStorage(): void {
    if (typeof window === 'undefined' || typeof localStorage === 'undefined') {
      return;
    }

    try {
      const storedToken = localStorage.getItem(this.TOKEN_KEY);
      if (storedToken) {
        this.token = storedToken;
        this.tokenExpiry = this.parseTokenExpiry(storedToken);
        this.log('Token loaded from storage');
      }
    } catch (error) {
      // Silently fail if localStorage is unavailable
      this.log('Failed to load token from storage:', error);
    }
  }

  /**
   * Save token to localStorage
   */
  private saveTokenToStorage(token: string): void {
    if (typeof window === 'undefined' || typeof localStorage === 'undefined') {
      return;
    }

    try {
      localStorage.setItem(this.TOKEN_KEY, token);
    } catch (error) {
      // Silently fail if localStorage quota exceeded or unavailable
      this.log('Failed to save token to storage:', error);
    }
  }

  /**
   * Remove token from localStorage
   */
  private removeTokenFromStorage(): void {
    if (typeof window === 'undefined' || typeof localStorage === 'undefined') {
      return;
    }

    try {
      localStorage.removeItem(this.TOKEN_KEY);
    } catch (error) {
      // Silently fail if localStorage is unavailable
      this.log('Failed to remove token from storage:', error);
    }
  }

  /**
   * Set the authentication token
   */
  setToken(token: string): void {
    this.token = token;
    this.tokenExpiry = this.parseTokenExpiry(token);
    this.saveTokenToStorage(token);
    if (this.tokenExpiry) {
      this.log('Token set, expires at:', new Date(this.tokenExpiry).toISOString());
    } else {
      this.log('Token set (no expiry - non-JWT token)');
    }
  }

  /**
   * Get current token
   */
  getToken(): string | null {
    return this.token;
  }

  /**
   * Clear the authentication token
   */
  clearToken(): void {
    this.token = null;
    this.tokenExpiry = null;
    this.removeTokenFromStorage();
    this.log('Token cleared');
  }

  /**
   * Check if token needs refresh
   */
  private needsRefresh(): boolean {
    if (!this.tokenExpiry) return false;

    const threshold = (this.config.refreshThreshold || 300) * 1000; // Convert to ms
    const now = Date.now();
    const timeUntilExpiry = this.tokenExpiry - now;

    return timeUntilExpiry <= threshold;
  }

  /**
   * Parse JWT token to get expiry time
   * Note: Sanctum tokens are not JWTs and don't contain expiry info
   */
  private parseTokenExpiry(token: string): number | null {
    try {
      // Check if this is a JWT (has 3 parts separated by dots)
      const parts = token.split('.');
      if (parts.length !== 3) {
        // Not a JWT (likely a Sanctum token), skip parsing
        return null;
      }

      const payload = parts[1];

      // Validate that the payload is valid base64 before attempting to decode
      if (!/^[A-Za-z0-9_-]+$/.test(payload)) {
        // Not valid base64url, likely not a JWT
        return null;
      }

      const decoded = JSON.parse(atob(payload));
      return decoded.exp ? decoded.exp * 1000 : null; // Convert to ms
    } catch (error) {
      // Token is not a valid JWT, likely a Sanctum token
      this.log('Failed to parse token expiry:', error instanceof Error ? error.message : error);
      return null;
    }
  }

  /**
   * Make HTTP request with automatic token refresh
   */
  async request<T>(
    endpoint: string,
    options: RequestInit = {},
    skipAuthCheck = false
  ): Promise<T> {
    if (!skipAuthCheck) {
      await this.ensureValidToken();
    }

    const url = `${this.config.apiUrl}${endpoint}`;
    const headers: Record<string, string> = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      ...(options.headers as Record<string, string> || {}),
    };

    if (this.token && !skipAuthCheck) {
      headers['Authorization'] = `Bearer ${this.token}`;
    }

    this.log(`${options.method || 'GET'} ${url}`);

    const response = await fetch(url, { ...options, headers });

    // Any 401 on an auth-required request invalidates the session
    if (response.status === 401 && !skipAuthCheck) {
      this.clearToken();
      throw {
        message: 'Session expired. Please log in again.',
        status: 401,
      } as ApiError;
    }

    return this.handleResponse<T>(response);
  }

  /**
   * Ensure we have a valid token, refresh if needed
   */
  private async ensureValidToken(): Promise<void> {
      if (!this.token) {
          throw new Error('Not authenticated. Please call setUserToken() first.');
      }

      if (this.isExpired()) {
          this.clearToken();
          throw { message: 'Session expired. Please log in again.', status: 401 } as ApiError;
      }

      // Move refresh logic into the method (remove the stray top-level block)
      if (this.needsRefresh()) {
          // If a refresh is already in progress, wait for it
          if (this.refreshPromise) {
              await this.refreshPromise;
              return;
          }

          // Start refresh
          this.refreshPromise = this.refreshToken();
          await this.refreshPromise;
          this.refreshPromise = null;
      }
  }

  /**
   * Refresh the authentication token
   * This is called automatically when token is close to expiry
   */
  async refreshToken(): Promise<void> {
    if (!this.token) {
      throw new Error('No token to refresh');
    }

    const url = `${this.config.apiUrl}/api/auth/refresh`;
    const headers: Record<string, string> = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': `Bearer ${this.token}`,
    };

    this.log('Refreshing token...');
    const response = await fetch(url, { method: 'POST', headers });

    if (!response.ok) {
      const contentType = response.headers.get('content-type');
      if (contentType?.includes('application/json')) {
        try {
          const errorData = await response.json();
          throw new Error(errorData.message || 'Token refresh failed');
        } catch {
          throw new Error('Token refresh failed');
        }
      }
      throw new Error('Token refresh failed');
    }

    const data = await response.json();
    const newToken = data.token;
    if (!newToken) {
      throw new Error('No token received from refresh endpoint');
    }

    this.setToken(newToken);
    this.log('Token refreshed successfully');
  }

  /**
   * Handle HTTP response
   */
  private async handleResponse<T>(response: Response): Promise<T> {
    const contentType = response.headers.get('content-type');
    const isJson = contentType?.includes('application/json');

    if (!response.ok) {
      let error: ApiError = {
        message: response.statusText,
        status: response.status,
      };

      if (isJson) {
        try {
          const errorData = await response.json();
          error = {
            message: errorData.message || error.message,
            errors: errorData.errors,
            status: response.status,
          };
        } catch (e) {
          // Use default error if JSON parsing fails
        }
      }

      this.log('API Error:', error);
      throw error;
    }

    if (isJson) {
      return response.json();
    }

    return {} as T;
  }

  /**
   * Debug logging
   */
  private log(...args: any[]): void {
    if (this.config.debug) {
      console.log('[PayCan SDK]', ...args);
    }
  }

  /**
   * GET request
   */
  async get<T>(endpoint: string, params?: Record<string, any>, skipAuthCheck = false): Promise<T> {
    let url = endpoint;
    if (params) {
      const queryString = new URLSearchParams(
        Object.entries(params).reduce((acc, [key, value]) => {
          if (value !== undefined && value !== null) {
            acc[key] = String(value);
          }
          return acc;
        }, {} as Record<string, string>)
      ).toString();
      if (queryString) {
        url = `${endpoint}?${queryString}`;
      }
    }
    return this.request<T>(url, { method: 'GET' }, skipAuthCheck);
  }

  /**
   * POST request
   */
  async post<T>(endpoint: string, data?: any, skipAuthCheck = false): Promise<T> {
    return this.request<T>(endpoint, {
      method: 'POST',
      body: data ? JSON.stringify(data) : undefined,
    }, skipAuthCheck);
  }

  /**
   * PUT request
   */
  async put<T>(endpoint: string, data?: any): Promise<T> {
    return this.request<T>(endpoint, {
      method: 'PUT',
      body: data ? JSON.stringify(data) : undefined,
    });
  }

  /**
   * DELETE request
   */
  async delete<T>(endpoint: string): Promise<T> {
    return this.request<T>(endpoint, { method: 'DELETE' });
  }

  private isExpired(): boolean {
      if (!this.tokenExpiry) return false;
      return Date.now() >= this.tokenExpiry;
  }
}
