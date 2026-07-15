/**
 * PayCan SDK - API-Only Client
 *
 * Exposes core API helpers + Checkout + CheckoutModal
 * Excludes Orders, Subscriptions, Transactions, ProductsModal, and change-plan flows.
 */
import { HttpClient } from './http-client';
import { CheckoutLite } from './resources/checkout-lite';
import { CheckoutModal, type CheckoutModalOptions } from './components/checkout-modal';
import type { PayCanConfig, AuthResponse, AuthenticationConfig, User } from './types';

export class PayCanApi {
  private http: HttpClient;
  public checkout: CheckoutLite;

  constructor(config: PayCanConfig) {
    this.validateConfig(config);

    this.http = new HttpClient(config);
    this.checkout = new CheckoutLite(this.http);
  }

  async me(): Promise<{ user: User }> {
    return this.http.get<{ user: User }>('/api/user/me');
  }

  setUserToken(token: string): void {
    if (!token) {
      throw new Error('Token is required');
    }
    this.http.setToken(token);
    this.log('User token set successfully');
  }

  async authenticate(config: AuthenticationConfig): Promise<AuthResponse> {
    try {
      const headers: Record<string, string> = {
        'Content-Type': 'application/json',
        ...config.headers,
      };

      const fetchOptions: RequestInit = { method: 'POST', headers };

      if (config.type === 'cookie') {
        fetchOptions.credentials = 'include';
      }

      if (config.data) {
        fetchOptions.body = JSON.stringify(config.data);
      }

      const response = await fetch(config.endpoint, fetchOptions);
      if (!response.ok) {
        throw new Error(`Authentication failed: ${response.statusText}`);
      }

      const data: AuthResponse = await response.json();
      if (!data.token) {
        throw new Error('No token received from auth endpoint');
      }

      this.http.setToken(data.token);
      this.log('User authenticated successfully');

      return data;
    } catch (error) {
      this.log('Authentication error:', error);
      throw error;
    }
  }

  isAuthenticated(): boolean {
    return this.http.getToken() !== null;
  }

  getToken(): string | null {
    return this.http.getToken();
  }

  logout(): void {
    this.http.clearToken();

    if (typeof window !== 'undefined' && typeof localStorage !== 'undefined') {
        try {
            Object.keys(localStorage).forEach((key) => {
                if (key.startsWith('paycan_')) {
                    localStorage.removeItem(key);
                }
            });
        } catch {}
    }

    this.log('User logged out');
  }

  async get<T = any>(url: string): Promise<T> {
    return this.http.get<T>(url);
  }

  async post<T = any>(url: string, data: any): Promise<T> {
    return this.http.post<T>(url, data);
  }

  openCheckoutModal(productId: number | string, options: Partial<CheckoutModalOptions> = {}): CheckoutModal {
    const modalOptions: CheckoutModalOptions = {
      productId,
      theme: options.theme || 'light',
      onSuccess: options.onSuccess,
      onCancel: options.onCancel,
      onError: options.onError,
    };

    const modal = new CheckoutModal(this as any, modalOptions);
    modal.open();

    this.log('Opening checkout modal for product:', productId);
    return modal;
  }

  openCheckoutModalPrice(priceId: number | string, options: Partial<CheckoutModalOptions> = {}): CheckoutModal {
    const modalOptions: CheckoutModalOptions = {
      priceId,
      theme: options.theme || 'light',
      onSuccess: options.onSuccess,
      onCancel: options.onCancel,
      onError: options.onError,
    };

    const modal = new CheckoutModal(this as any, modalOptions);
    modal.open();

    this.log('Opening checkout modal for price:', priceId);
    return modal;
  }

  private validateConfig(config: PayCanConfig): void {
    if (!config.apiUrl) {
      throw new Error('PayCan: apiUrl is required');
    }
    config.apiUrl = config.apiUrl.replace(/\/$/, '');
  }

  private log(...args: any[]): void {
    const config = (this.http as any)['config'];
    if (config?.debug) {
      console.log('[PayCan SDK]', ...args);
    }
  }

  async refreshToken(): Promise<void> {
    await this.http.refreshToken();
  }
}