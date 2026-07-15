/**
 * React Example - Complete integration
 */

import React, { createContext, useContext, useEffect, useState, ReactNode } from 'react';
import PayCan from '@paycan/sdk';
import type { Order, Subscription } from '@paycan/sdk';

// ==========================================
// 1. Create PayCan Context
// ==========================================

interface PayCanContextType {
  paycan: PayCan | null;
  isAuthenticated: boolean;
  loading: boolean;
  error: string | null;
}

const PayCanContext = createContext<PayCanContextType>({
  paycan: null,
  isAuthenticated: false,
  loading: true,
  error: null,
});

export const usePayCan = () => useContext(PayCanContext);

// ==========================================
// 2. PayCan Provider Component
// ==========================================

interface PayCanProviderProps {
  children: ReactNode;
  config: {
    apiUrl: string;
    tokenEndpoint?: string; // Optional: endpoint to get PayCan token
  };
  autoFetchToken?: boolean; // Auto-fetch token on mount (requires tokenEndpoint)
}

export function PayCanProvider({ children, config, autoFetchToken = true }: PayCanProviderProps) {
  const [paycan] = useState(() => new PayCan({ apiUrl: config.apiUrl }));
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [loading, setLoading] = useState(autoFetchToken);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    async function fetchAndSetToken() {
      if (!autoFetchToken || !config.tokenEndpoint) {
        setLoading(false);
        return;
      }

      try {
        // Get token from your backend
        const response = await fetch(config.tokenEndpoint);
        const { token } = await response.json();

        // Set the token
        paycan.setUserToken(token);
        setIsAuthenticated(true);
        setError(null);
      } catch (err: any) {
        setError(err.message || 'Failed to get token');
        setIsAuthenticated(false);
      } finally {
        setLoading(false);
      }
    }

    fetchAndSetToken();
  }, [autoFetchToken, config.tokenEndpoint, paycan]);

  return (
    <PayCanContext.Provider value={{ paycan, isAuthenticated, loading, error }}>
      {children}
    </PayCanContext.Provider>
  );
}

// ==========================================
// 3. Example: Orders List Component
// ==========================================

export function OrdersList() {
  const { paycan, isAuthenticated, loading: authLoading } = usePayCan();
  const [orders, setOrders] = useState<Order[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    async function loadOrders() {
      if (!isAuthenticated || !paycan) return;

      try {
        setLoading(true);
        // List orders with filtering, sorting, and includes
        const response = await paycan.orders.list({
          filter: { status: 'completed' },
          include: 'productPrice.product',
          sort: '-created_at',
          per_page: 20
        });
        setOrders(response.data || []);
        setError(null);
      } catch (err: any) {
        setError(err.message || 'Failed to load orders');
      } finally {
        setLoading(false);
      }
    }

    loadOrders();
  }, [isAuthenticated, paycan]);

  if (authLoading) return <div>Authenticating...</div>;
  if (!isAuthenticated) return <div>Please log in</div>;
  if (loading) return <div>Loading orders...</div>;
  if (error) return <div>Error: {error}</div>;

  return (
    <div className="orders-list">
      <h2>Your Orders</h2>
      {orders.length === 0 ? (
        <p>No orders yet</p>
      ) : (
        <ul>
          {orders.map((order) => (
            <li key={order.id}>
              <h3>Order #{order.order_number}</h3>
              <p>Status: {order.status}</p>
              <p>Total: ${order.total} {order.currency}</p>
              <p>Date: {new Date(order.created_at).toLocaleDateString()}</p>
            </li>
          ))}
        </ul>
      )}
    </div>
  );
}

// ==========================================
// 4. Example: Subscription Access Gate
// ==========================================

interface SubscriptionGateProps {
  productId: string;
  children: ReactNode;
  fallback?: ReactNode;
}

export function SubscriptionGate({ productId, children, fallback }: SubscriptionGateProps) {
  const { paycan, isAuthenticated } = usePayCan();
  const [hasAccess, setHasAccess] = useState(false);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function checkAccess() {
      if (!isAuthenticated || !paycan) {
        setHasAccess(false);
        setLoading(false);
        return;
      }

      try {
        const activeSubscriptions = await paycan.subscriptions.listActive();
        const access = activeSubscriptions.some(
          (sub) => String(sub.product?.id) === String(productId)
        );
        setHasAccess(access);
      } catch (err) {
        setHasAccess(false);
      } finally {
        setLoading(false);
      }
    }

    checkAccess();
  }, [productId, isAuthenticated, paycan]);

  if (loading) return <div>Checking access...</div>;

  if (hasAccess) {
    return <>{children}</>;
  }

  return <>{fallback || <div>Premium subscription required</div>}</>;
}

// ==========================================
// 5. Example: Checkout Button
// ==========================================

interface CheckoutButtonProps {
  productId: string;
  priceId: string;
  label?: string;
  className?: string;
}

export function CheckoutButton({ productId, priceId, label = 'Buy Now', className }: CheckoutButtonProps) {
  const { paycan, isAuthenticated } = usePayCan();
  const [loading, setLoading] = useState(false);

  const handleCheckout = async () => {
    if (!paycan || !isAuthenticated) {
      alert('Please log in first');
      return;
    }

    try {
      setLoading(true);
      const session = await paycan.checkout.create({
        product_id: productId,
        product_price_id: priceId,
        gateway: 'stripe',
      });

      window.location.href = session.checkout_url;
    } catch (err: any) {
      alert('Checkout failed: ' + err.message);
      setLoading(false);
    }
  };

  return (
    <button onClick={handleCheckout} disabled={loading || !isAuthenticated} className={className}>
      {loading ? 'Processing...' : label}
    </button>
  );
}

// ==========================================
// 6. Example: Subscription Manager
// ==========================================

export function SubscriptionManager() {
  const { paycan, isAuthenticated } = usePayCan();
  const [subscriptions, setSubscriptions] = useState<Subscription[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadSubscriptions();
  }, [isAuthenticated, paycan]);

  const loadSubscriptions = async () => {
    if (!isAuthenticated || !paycan) return;

    try {
      setLoading(true);
      // List subscriptions with filtering and includes
      const response = await paycan.subscriptions.list({
        filter: { status: 'active' },
        include: 'productPrice.product',
        sort: '-created_at'
      });
      setSubscriptions(response.data || []);
    } catch (err) {
      console.error('Failed to load subscriptions:', err);
    } finally {
      setLoading(false);
    }
  };

  const handleCancel = async (subId: string) => {
    if (!paycan || !confirm('Are you sure you want to cancel?')) return;

    try {
      await paycan.subscriptions.cancel(subId);
      await loadSubscriptions();
    } catch (err: any) {
      alert('Failed to cancel: ' + err.message);
    }
  };

  const handleResume = async (subId: string) => {
    if (!paycan) return;

    try {
      await paycan.subscriptions.resume(subId);
      await loadSubscriptions();
    } catch (err: any) {
      alert('Failed to resume: ' + err.message);
    }
  };

  if (loading) return <div>Loading subscriptions...</div>;

  return (
    <div className="subscriptions">
      <h2>Your Subscriptions</h2>
      {subscriptions.length === 0 ? (
        <p>No active subscriptions</p>
      ) : (
        <ul>
          {subscriptions.map((sub) => (
            <li key={sub.id}>
              <h3>{sub.product_price?.product?.title || 'Subscription'}</h3>
              <p>Status: {sub.status}</p>
              <p>Price: ${sub.product_price?.amount} / {sub.product_price?.billing_period}</p>
              {sub.current_period_end && (
                <p>Next billing: {new Date(sub.current_period_end).toLocaleDateString()}</p>
              )}

              {sub.status === 'active' && (
                <button onClick={() => handleCancel(sub.id)}>Cancel Subscription</button>
              )}

              {sub.status === 'cancelled' && sub.cancel_at && (
                <button onClick={() => handleResume(sub.id)}>Resume Subscription</button>
              )}
            </li>
          ))}
        </ul>
      )}
    </div>
  );
}

// ==========================================
// 7. Example: Main App Component
// ==========================================

export function App() {
  return (
    <PayCanProvider
      config={{
        apiUrl: 'https://pay.yourapp.com',
        tokenEndpoint: '/api/paycan/token',
      }}
      autoFetchToken={true} // Automatically fetch token on mount
    >
      <div className="app">
        <h1>My App</h1>

        {/* Premium content protected by subscription */}
        <SubscriptionGate
          productId="premium-product"
          fallback={<CheckoutButton priceId="price-premium-monthly" label="Upgrade to Premium" />}
        >
          <div className="premium-content">
            <h2>Premium Content</h2>
            <p>This is only visible to premium subscribers!</p>
          </div>
        </SubscriptionGate>

        {/* Orders list */}
        <OrdersList />

        {/* Subscription manager */}
        <SubscriptionManager />
      </div>
    </PayCanProvider>
  );
}
