import { ref, computed } from 'vue'
import type { Ref } from 'vue'

export interface PaymentGateway {
  id: string
  name: string
  icon: string
  description: string
  supports_subscriptions: boolean
  supported_currencies?: string[]
  supported_product_types?: string[]
}

export interface PaymentGatewayValidation {
  valid: boolean
  gateway?: string
  errors?: Record<string, string[]>
}

export function usePaymentGateways() {
  const gateways: Ref<PaymentGateway[]> = ref([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  /**
   * Fetch all available payment gateways
   */
  const fetchGateways = async (): Promise<PaymentGateway[]> => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch('/api/payment-gateways')
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }
      
      const data = await response.json()
      gateways.value = data.data || data
      return gateways.value
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to fetch payment gateways'
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Fetch payment gateways for a specific product
   */
  const fetchGatewaysForProduct = async (productId: string): Promise<PaymentGateway[]> => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(`/api/payment-gateways/products/${productId}`)
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }
      
      const data = await response.json()
      const productGateways = data.data || data
      gateways.value = productGateways
      return productGateways
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to fetch payment gateways for product'
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Fetch payment gateways for a specific product price
   */
  const fetchGatewaysForProductPrice = async (productPriceId: string): Promise<PaymentGateway[]> => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(`/api/payment-gateways/product-prices/${productPriceId}`)
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }
      
      const data = await response.json()
      const priceGateways = data.data || data
      gateways.value = priceGateways
      return priceGateways
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to fetch payment gateways for product price'
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Validate a payment gateway for checkout
   */
  const validateGateway = async (
    gateway: string,
    options: {
      productId?: string
      productPriceId?: string
    } = {}
  ): Promise<PaymentGatewayValidation> => {
    loading.value = true
    error.value = null

    try {
      const body: Record<string, string> = { gateway }
      
      if (options.productId) {
        body.product_id = options.productId
      }
      
      if (options.productPriceId) {
        body.product_price_id = options.productPriceId
      }

      const response = await fetch('/api/payment-gateways/validate', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify(body),
      })

      const data = await response.json()

      if (!response.ok) {
        return {
          valid: false,
          errors: data.errors || { gateway: [data.message || 'Validation failed'] }
        }
      }

      return {
        valid: data.data?.valid || data.valid || true,
        gateway: data.data?.gateway || data.gateway
      }
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to validate payment gateway'
      return {
        valid: false,
        errors: { gateway: [error.value] }
      }
    } finally {
      loading.value = false
    }
  }

  /**
   * Get gateways that support subscriptions
   */
  const subscriptionGateways = computed(() => 
    gateways.value.filter(gateway => gateway.supports_subscriptions)
  )

  /**
   * Get gateways that support one-time payments
   */
  const oneTimeGateways = computed(() => 
    gateways.value.filter(gateway => !gateway.supports_subscriptions || gateway.supports_subscriptions)
  )

  /**
   * Check if a specific gateway is available
   */
  const isGatewayAvailable = (gatewayId: string): boolean => {
    return gateways.value.some(gateway => gateway.id === gatewayId)
  }

  /**
   * Get gateway by ID
   */
  const getGateway = (gatewayId: string): PaymentGateway | undefined => {
    return gateways.value.find(gateway => gateway.id === gatewayId)
  }

  /**
   * Filter gateways by currency support
   */
  const getGatewaysForCurrency = (currency: string): PaymentGateway[] => {
    return gateways.value.filter(gateway => 
      !gateway.supported_currencies || gateway.supported_currencies.includes(currency)
    )
  }

  /**
   * Filter gateways by product type support
   */
  const getGatewaysForProductType = (productType: string): PaymentGateway[] => {
    return gateways.value.filter(gateway => 
      !gateway.supported_product_types || gateway.supported_product_types.includes(productType)
    )
  }

  return {
    // State
    gateways: readonly(gateways),
    loading: readonly(loading),
    error: readonly(error),

    // Actions
    fetchGateways,
    fetchGatewaysForProduct,
    fetchGatewaysForProductPrice,
    validateGateway,

    // Computed
    subscriptionGateways,
    oneTimeGateways,

    // Utilities
    isGatewayAvailable,
    getGateway,
    getGatewaysForCurrency,
    getGatewaysForProductType,
  }
}

// Example usage:
/*
// In a Vue component
import { usePaymentGateways } from '@/composables/usePaymentGateways'

export default {
  setup() {
    const {
      gateways,
      loading,
      error,
      fetchGatewaysForProductPrice,
      validateGateway,
      subscriptionGateways
    } = usePaymentGateways()

    // Fetch gateways for a specific product price
    const loadGateways = async (productPriceId: string) => {
      try {
        await fetchGatewaysForProductPrice(productPriceId)
      } catch (err) {
        console.error('Failed to load gateways:', err)
      }
    }

    // Validate gateway before checkout
    const handleCheckout = async (gateway: string, productPriceId: string) => {
      const validation = await validateGateway(gateway, { productPriceId })
      
      if (!validation.valid) {
        console.error('Gateway validation failed:', validation.errors)
        return
      }
      
      // Proceed with checkout...
    }

    return {
      gateways,
      loading,
      error,
      loadGateways,
      handleCheckout,
      subscriptionGateways
    }
  }
}
*/