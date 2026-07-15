/**
 * PayCan SDK
 *
 * Official JavaScript SDK for PayCan - Payment integration made simple
 *
 * @packageDocumentation
 */

export { PayCan } from './paycan';
export { CheckoutModal } from './components/checkout-modal';
export { SubscriptionsModal } from './components/subscriptions-modal';
export { OrdersModal } from './components/orders-modal';
export { TransactionsModal } from './components/transactions-modal';
export { ProductsModal } from './components/products-modal';
export type { CheckoutModalOptions } from './components/checkout-modal';
export type { SubscriptionsModalOptions } from './components/subscriptions-modal';
export type { OrdersModalOptions } from './components/orders-modal';
export type { TransactionsModalOptions } from './components/transactions-modal';
export type { ProductsModalOptions } from './components/products-modal';
export type {
  PayCanConfig,
  AuthenticationType,
  AuthenticationConfig,
  AuthResponse,
  User,
  Order,
  ProductPrice,
  Product,
  Transaction,
  Fulfillment,
  Subscription,
  DownloadLink,
  LicenseKey,
  CheckoutSession,
  PaginatedResponse,
  ApiError,
  CreateOrderData,
  CreateCheckoutData,
  CheckoutPreviewResponse,
  CheckoutPreviewProduct,
  CheckoutPreviewPrice,
  CheckoutPreviewGateway,
  GetCheckoutPreviewParams,
} from './types';

// Default export
export { PayCan as default } from './paycan';
