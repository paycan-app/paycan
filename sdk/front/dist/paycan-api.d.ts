import { CheckoutLite } from './resources/checkout-lite';
import { CheckoutModal, type CheckoutModalOptions } from './components/checkout-modal';
import type { PayCanConfig, AuthResponse, AuthenticationConfig, User } from './types';
export declare class PayCanApi {
    private http;
    checkout: CheckoutLite;
    constructor(config: PayCanConfig);
    me(): Promise<{
        user: User;
    }>;
    setUserToken(token: string): void;
    authenticate(config: AuthenticationConfig): Promise<AuthResponse>;
    isAuthenticated(): boolean;
    getToken(): string | null;
    logout(): void;
    get<T = any>(url: string): Promise<T>;
    post<T = any>(url: string, data: any): Promise<T>;
    openCheckoutModal(productId: number | string, options?: Partial<CheckoutModalOptions>): CheckoutModal;
    openCheckoutModalPrice(priceId: number | string, options?: Partial<CheckoutModalOptions>): CheckoutModal;
    private validateConfig;
    private log;
    refreshToken(): Promise<void>;
}
//# sourceMappingURL=paycan-api.d.ts.map