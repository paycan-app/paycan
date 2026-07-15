<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import { PayCan } from '@paycan/sdk';
import { Button } from '@/components/ui/button';

const props = defineProps<{
  orderId?: string | number;
  apiBaseUrl: string;
  clientUrl?: string;
}>();

const apiUrl = props.apiBaseUrl || (typeof localStorage !== 'undefined' ? localStorage.getItem('paycan_api_url') || '' : '');
const token = typeof localStorage !== 'undefined' ? localStorage.getItem('paycan_user_token') : null;

const apiClient = new PayCan({ apiUrl });
if (token) {
  apiClient.setUserToken(token);
}

const order = ref<any | null>(null);
const loading = ref(false);
const error = ref<string | null>(null);

const formatPrice = (amount: number, currency: string) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: currency.toUpperCase(),
  }).format(amount);
};

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

onMounted(async () => {
  if (!props.orderId || !token) {
    return;
  }
  loading.value = true;
  try {
    const res = await apiClient.orders.get(String(props.orderId));
    order.value = res?.data || null;
  } catch (e: any) {
    error.value = e?.message || 'Failed to load order details';
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <Head title="Payment Successful" />
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center p-4 sm:p-6">
    <div class="w-full max-w-2xl">
      <!-- Success Card -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Success Header with SVG -->
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-950 dark:to-emerald-950 px-6 py-8 sm:px-8 sm:py-12 text-center border-b border-gray-200 dark:border-gray-700">
          <!-- Success SVG Illustration -->
          <div class="flex justify-center mb-6">
            <svg class="w-24 h-24 sm:w-32 sm:h-32" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
              <!-- Outer circle with gradient -->
              <circle cx="60" cy="60" r="58" class="fill-green-100 dark:fill-green-900/30" />
              <circle cx="60" cy="60" r="50" class="fill-green-500 dark:fill-green-600" />

              <!-- Check mark -->
              <path d="M35 60 L50 75 L85 40" stroke="white" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" class="animate-[draw_0.5s_ease-in-out]" />

              <!-- Sparkle effects -->
              <circle cx="30" cy="30" r="3" class="fill-green-400 dark:fill-green-500 animate-pulse" />
              <circle cx="90" cy="25" r="2" class="fill-green-400 dark:fill-green-500 animate-pulse" style="animation-delay: 0.2s" />
              <circle cx="95" cy="70" r="3" class="fill-green-400 dark:fill-green-500 animate-pulse" style="animation-delay: 0.4s" />
              <circle cx="25" cy="80" r="2" class="fill-green-400 dark:fill-green-500 animate-pulse" style="animation-delay: 0.3s" />
            </svg>
          </div>

          <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white mb-2">
            Payment Successful!
          </h1>
          <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300">
            Thank you for your purchase. Your order has been confirmed.
          </p>
        </div>

        <!-- Order Details -->
        <div class="px-6 py-6 sm:px-8 sm:py-8">
          <div v-if="loading" class="flex flex-col items-center justify-center py-12">
            <div class="w-10 h-10 border-3 border-green-500 dark:border-green-400 border-t-transparent rounded-full animate-spin mb-4"></div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Loading order details...</p>
          </div>

          <div v-else-if="error" class="flex flex-col items-center justify-center py-12">
            <svg class="w-16 h-16 text-red-500 dark:text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>
          </div>

          <div v-else-if="order" class="space-y-6">
            <!-- Order Number -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
              <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Order Number</span>
              <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ order.order_number }}</span>
            </div>

            <!-- Product Details -->
            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
              <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Order Details</h3>
              <div class="space-y-3">
                <div class="flex justify-between">
                  <span class="text-sm text-gray-600 dark:text-gray-400">Product</span>
                  <span class="text-sm font-medium text-gray-900 dark:text-white text-right">{{ order.product?.title || order.product?.name || 'N/A' }}</span>
                </div>
                <div v-if="order.product_price" class="flex justify-between">
                  <span class="text-sm text-gray-600 dark:text-gray-400">Plan</span>
                  <span class="text-sm font-medium text-gray-900 dark:text-white">{{ order.product_price.title }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-sm text-gray-600 dark:text-gray-400">Quantity</span>
                  <span class="text-sm font-medium text-gray-900 dark:text-white">{{ order.quantity || 1 }}</span>
                </div>
                <div v-if="order.tax && order.tax > 0" class="flex justify-between">
                  <span class="text-sm text-gray-600 dark:text-gray-400">Tax</span>
                  <span class="text-sm font-medium text-gray-900 dark:text-white">{{ formatPrice(order.tax, order.currency) }}</span>
                </div>
                <div class="flex justify-between pt-3 border-t border-gray-200 dark:border-gray-700">
                  <span class="text-base font-semibold text-gray-900 dark:text-white">Total</span>
                  <span class="text-base font-bold text-green-600 dark:text-green-500">{{ formatPrice(order.total, order.currency) }}</span>
                </div>
              </div>
            </div>

            <!-- Status & Date -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="flex flex-col gap-1">
                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Status</span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 w-fit">
                  {{ order.status || 'Completed' }}
                </span>
              </div>
              <div v-if="order.created_at" class="flex flex-col gap-1">
                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Date</span>
                <span class="text-sm text-gray-900 dark:text-white">{{ formatDate(order.created_at) }}</span>
              </div>
            </div>

            <!-- Payment Method -->
            <div v-if="order.gateway" class="flex items-center justify-between py-3 px-4 bg-blue-50 dark:bg-blue-950/30 rounded-lg border border-blue-200 dark:border-blue-900">
              <span class="text-sm text-gray-600 dark:text-gray-400">Payment Method</span>
              <span class="text-sm font-medium text-gray-900 dark:text-white capitalize">{{ order.gateway }}</span>
            </div>
          </div>

          <div v-else class="text-center py-8">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
              Order <span v-if="props.orderId" class="font-semibold">#{{ props.orderId }}</span> has been confirmed.
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-500">
              A confirmation email has been sent to your email address.
            </p>
          </div>
        </div>

        <!-- Footer Actions -->
        <div v-if="props.clientUrl" class="px-6 py-4 sm:px-8 sm:py-6 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex justify-center">
          <Button as="a" :href="props.clientUrl" class="w-full sm:w-auto">
            Back to Application
          </Button>
        </div>
      </div>

      <!-- Additional Info -->
      <div class="mt-6 text-center">
        <!-- 
        <p class="text-xs text-gray-500 dark:text-gray-400">
          Need help? Contact us at
          <a href="mailto:support@example.com" class="text-green-600 dark:text-green-500 hover:underline">support@example.com</a>
        </p> 
        -->
      </div>
    </div>
  </div>
</template>