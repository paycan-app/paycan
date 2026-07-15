<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import { PayCan } from '@paycan/sdk';

import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem } from '@/components/ui/dropdown-menu';
import { Toaster } from '@/components/ui/sonner';
import { toast } from 'vue-sonner';
import { ShoppingCart, CreditCard, Loader2, AlertCircle, Check } from 'lucide-vue-next';

type GatewayKey = 'stripe' | 'paypal';

const props = defineProps<{
  userToken?: string;
  apiBaseUrl: string;
  initialProductId?: string | number;
  initialPriceId?: string | number;
}>();

const apiClient = new PayCan({ apiUrl: props.apiBaseUrl });

// Set token if provided (stateless)
if (props.userToken) {
  apiClient.setUserToken(props.userToken);
  try {
    localStorage.setItem('paycan_user_token', props.userToken);
  } catch {}
}
try {
  localStorage.setItem('paycan_api_url', props.apiBaseUrl);
} catch {}

const loading = ref(false);
const error = ref<string | null>(null);

const products = ref<any[]>([]);
const selectedProduct = ref<any | null>(null);
const selectedPriceId = ref<number | null>(null);
const quantity = ref(1);

// Location/tax (optional)
const billingCountry = ref<string | null>(null);
const billingState = ref<string | null>(null);

// Guest checkout fallback (only used if no token)
const billingEmail = ref('');
const billingName = ref('');

// Preview state
const previewData = ref<any | null>(null);
const previewLoading = ref(false);
const previewError = ref<string | null>(null);

// Show redirect state and suppress preview errors while redirecting
const redirecting = ref(false);

// Gateways from preview
const availableGateways = ref<{ key: GatewayKey; name: string }[]>([]);
const selectedGateway = ref<GatewayKey>('stripe');

// Utilities
const formatPrice = (amount: number, currency: string) =>
  new Intl.NumberFormat('en-US', { style: 'currency', currency: currency || 'USD' }).format(amount || 0);

const pricesForSelected = computed(() => {
  if (previewData.value?.prices?.length) return previewData.value.prices;
  return selectedProduct.value?.prices || [];
});

const isPriceDropdown = computed(() => (pricesForSelected.value?.length || 0) > 3);

const selectedPrice = computed(() => {
  // Prefer preview selected
  if (previewData.value?.selected_price) return previewData.value.selected_price;
  const list = pricesForSelected.value || [];
  return list.find((p: any) => p.id === selectedPriceId.value) || null;
});

const billingPeriodText = computed(() => {
  const period = selectedPrice.value?.billing_period || 'once';
  return period === 'once' ? 'one-time payment' : `per ${period}`;
});

// Load preview
const loadPreview = async () => {
  if (!selectedPriceId.value) return;
  previewLoading.value = true;
  previewError.value = null;

  try {
    const params: any = {
      product_price_id: selectedPriceId.value,
      gateway: selectedGateway.value,
      quantity: quantity.value,
    };
    if (billingCountry.value) params.billing_country = billingCountry.value;
    if (billingState.value) params.billing_state = billingState.value;

    const response = await apiClient.checkout.preview(params);
    previewData.value = response;
    availableGateways.value = (response?.payment_methods || [])
      .map((m: any) => ({ key: m.key as GatewayKey, name: m.name }))
      .filter((g: any) => ['stripe', 'paypal'].includes(g.key));
    if (!availableGateways.value.find(g => g.key === selectedGateway.value) && availableGateways.value.length) {
      selectedGateway.value = availableGateways.value[0].key;
    }
  } catch (e: any) {
    console.error('Failed to load preview:', e);
    // Suppress preview error message while redirecting to gateway
    if (!redirecting.value) {
      previewError.value = e?.message || 'Failed to load checkout preview';
    }
  } finally {
    previewLoading.value = false;
  }
};

// Initialize selection and data
const initData = async () => {
  loading.value = true;
  error.value = null;

  try {
    // If we have an initial price, preview directly and infer product
    if (props.initialPriceId) {
      selectedPriceId.value = Number(props.initialPriceId);
      await loadPreview();
      selectedProduct.value = previewData.value?.product || null;
      return;
    }

    // If we have an initial product, load product with prices and preview first price
    if (props.initialProductId) {
      const { data } = await apiClient.products.get(String(props.initialProductId), { include: 'prices' });
      selectedProduct.value = data;
      const firstPrice = (data?.prices || [])[0];
      selectedPriceId.value = firstPrice?.id || null;
      if (selectedPriceId.value) await loadPreview();
      return;
    }

    // Else: list products and choose the first active / price
    const res = await apiClient.products.list({ include: 'prices', sort: 'title', per_page: 20 });
    products.value = res?.data || [];
    selectedProduct.value = products.value[0] || null;
    const firstPrice = (selectedProduct.value?.prices || [])[0];
    selectedPriceId.value = firstPrice?.id || null;
    if (selectedPriceId.value) await loadPreview();
  } catch (e: any) {
    console.error(e);
    error.value = e?.message || 'Failed to initialize checkout data';
  } finally {
    loading.value = false;
  }
};

onMounted(initData);

// Reload preview on changes
watch([selectedPriceId, selectedGateway, quantity, billingCountry, billingState], () => {
  if (selectedPriceId.value && props.userToken) loadPreview();
});

// Checkout
const handleCheckout = async () => {
  if (!selectedPriceId.value || !previewData.value?.product?.id) {
    toast.error('Please select a price to continue');
    return;
  }

  const data: any = {
    product_id: previewData.value.product.id,
    product_price_id: selectedPriceId.value,
    gateway: selectedGateway.value,
    quantity: quantity.value,
  };

  // Guest fallback: add billing email/name if no token
  if (!props.userToken) {
    if (!billingEmail.value || !billingName.value) {
      toast.error('Please enter your billing email and name');
      return;
    }
    data.billing_email = billingEmail.value;
    data.billing_name = billingName.value;
  }

  // Optional: pass location for accurate tax
  if (billingCountry.value) data.billing_country = billingCountry.value;
  if (billingState.value) data.billing_state = billingState.value;

  try {
    redirecting.value = true;
    const gatewayLabel = selectedGateway.value === 'paypal' ? 'PayPal' : 'Stripe';
    toast.info(`Redirecting to ${gatewayLabel}...`, { duration: 5000 });

    const session = await apiClient.checkout.create(data);

    // Small delay so user sees the message before navigation
    await new Promise((r) => setTimeout(r, 300));

    window.location.href = session.checkout_url;
  } catch (e: any) {
    redirecting.value = false;
    console.error(e);
    toast.error(e?.message || 'Failed to start checkout');
  }
};
</script>

<template>
  <Head title="Checkout" />
  <Toaster position="top-right" />

  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-950 p-6">
    <div class="mx-auto max-w-3xl">
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
          <ShoppingCart class="h-8 w-8" />
          Checkout
        </h1>
        <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
          Select a price and payment method, then complete your purchase
        </p>
      </div>

      <Card class="border-gray-200 dark:border-gray-800 shadow-sm">
        <CardHeader>
          <CardTitle class="flex items-center justify-between">
            <span>{{ selectedProduct?.title || 'Select a product' }}</span>
            <Badge v-if="selectedProduct?.type" class="capitalize">
              {{ selectedProduct?.type }}
            </Badge>
          </CardTitle>
          <CardDescription>
            {{ selectedProduct?.description }}
          </CardDescription>
        </CardHeader>

        <CardContent class="space-y-6">
          <!-- Price Selection -->
          <div class="space-y-3">
            <Label class="text-sm font-medium">Select Price</Label>

            <!-- Dropdown for many prices -->
            <div v-if="isPriceDropdown" class="w-full">
              <DropdownMenu>
                <DropdownMenuTrigger asChild>
                  <Button variant="outline" class="w-full justify-between">
                    <span>
                      {{
                        selectedPrice
                          ? `${selectedPrice.title} (${formatPrice(selectedPrice.final_price ?? selectedPrice.amount, selectedPrice.currency || 'USD')})`
                          : 'Choose a price'
                      }}
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ billingPeriodText }}</span>
                  </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent class="w-80">
                  <DropdownMenuItem
                    v-for="p in pricesForSelected"
                    :key="p.id"
                    class="cursor-pointer flex items-center justify-between"
                    @click="selectedPriceId = p.id"
                  >
                    <div class="flex flex-col">
                      <span class="font-medium">{{ p.title }}</span>
                      <span class="text-xs text-gray-500 dark:text-gray-400">{{ p.billing_period === 'once' ? 'one-time' : p.billing_period }}</span>
                    </div>
                    <span class="font-semibold">
                      {{ formatPrice(p.final_price ?? p.amount, p.currency || 'USD') }}
                    </span>
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
            </div>

            <!-- Radio group for up to 3 prices -->
            <RadioGroup v-else v-model="selectedPriceId" class="space-y-2">
              <div
                v-for="p in pricesForSelected"
                :key="p.id"
                class="flex cursor-pointer items-center space-x-3 rounded-lg border-2 p-4 transition-all hover:bg-gray-50 dark:hover:bg-gray-800/50"
                :class="selectedPriceId === p.id ? 'border-primary bg-primary/5 dark:bg-primary/10' : 'border-gray-200 dark:border-gray-700'"
                @click="selectedPriceId = p.id"
              >
                <RadioGroupItem :id="`price-${p.id}`" :value="p.id" />
                <Label :for="`price-${p.id}`" class="flex flex-1 cursor-pointer items-center justify-between">
                  <div>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ p.title }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                      {{ p.billing_period === 'once' ? 'one-time payment' : `per ${p.billing_period}` }}
                    </p>
                  </div>
                  <div class="text-right">
                    <p class="font-bold text-gray-900 dark:text-white">
                      {{ formatPrice(p.final_price ?? p.amount, p.currency || 'USD') }}
                    </p>
                  </div>
                </Label>
              </div>
            </RadioGroup>
          </div>

          <!-- Quantity -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="space-y-2">
              <Label>Quantity</Label>
              <input
                type="number"
                min="1"
                class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 dark:bg-gray-800 dark:text-white dark:border-gray-700"
                v-model.number="quantity"
              />
            </div>

            <div class="space-y-2">
              <Label>Billing Country (optional)</Label>
              <input
                type="text"
                placeholder="US"
                class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 dark:bg-gray-800 dark:text-white dark:border-gray-700"
                v-model="billingCountry"
              />
            </div>

            <div class="space-y-2">
              <Label>Billing State/Region (optional)</Label>
              <input
                type="text"
                placeholder="CA"
                class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 dark:bg-gray-800 dark:text-white dark:border-gray-700"
                v-model="billingState"
              />
            </div>
          </div>

          <!-- Preview -->
          <div class="space-y-3 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
            <div v-if="previewLoading" class="flex items-center justify-center py-2">
              <Loader2 class="h-5 w-5 animate-spin text-primary" />
              <span class="ml-2 text-sm text-gray-500">Calculating total...</span>
            </div>

            <!-- Suppress error message while redirecting -->
            <div v-else-if="previewError && !redirecting" class="rounded-lg bg-red-50 p-3 dark:bg-red-900/20">
              <div class="flex items-start gap-2">
                <AlertCircle class="h-4 w-4 text-red-600 dark:text-red-400 mt-0.5 shrink-0" />
                <p class="text-sm text-red-700 dark:text-red-300">{{ previewError }}</p>
              </div>
            </div>

            <div v-else class="space-y-2">
              <div class="flex items-baseline justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                <span class="font-medium text-gray-900 dark:text-white">
                  {{ formatPrice(selectedPrice?.subtotal ?? selectedPrice?.amount ?? 0, selectedPrice?.currency || 'USD') }}
                </span>
              </div>              <Separator class="my-2" />
              <div class="flex items-baseline justify-between">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Total</span>
                <div class="text-right">
                  <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ formatPrice(selectedPrice?.final_price ?? selectedPrice?.amount ?? 0, selectedPrice?.currency || 'USD') }}
                  </p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">{{ billingPeriodText }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Payment Gateways -->
          <div class="space-y-3">
            <Label class="text-sm font-medium">Select Payment Method</Label>
            <RadioGroup v-model="selectedGateway" class="space-y-2">
              <!-- Stripe -->
              <div
                class="flex cursor-pointer items-center space-x-3 rounded-lg border-2 p-4 transition-all hover:bg-gray-50 dark:hover:bg-gray-800/50"
                :class="selectedGateway === 'stripe' ? 'border-primary bg-primary/5 dark:bg-primary/10' : 'border-gray-200 dark:border-gray-700'"
                @click="selectedGateway = 'stripe'"
                v-if="availableGateways.length === 0 || availableGateways.find(g => g.key === 'stripe')"
              >
                <RadioGroupItem id="stripe" value="stripe" />
                <Label for="stripe" class="flex flex-1 cursor-pointer items-center justify-between">
                  <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-900/30">
                      <CreditCard class="h-5 w-5 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <div>
                      <p class="font-semibold text-gray-900 dark:text-white">Stripe</p>
                      <p class="text-xs text-gray-600 dark:text-gray-400">Card, Apple Pay, Google Pay</p>
                    </div>
                  </div>
                  <Badge v-if="selectedGateway === 'stripe'" variant="default" class="text-xs">Selected</Badge>
                </Label>
              </div>

              <!-- PayPal -->
              <div
                class="flex cursor-pointer items-center space-x-3 rounded-lg border-2 p-4 transition-all hover:bg-gray-50 dark:hover:bg-gray-800/50"
                :class="selectedGateway === 'paypal' ? 'border-primary bg-primary/5 dark:bg-primary/10' : 'border-gray-200 dark:border-gray-700'"
                @click="selectedGateway = 'paypal'"
                v-if="availableGateways.length === 0 || availableGateways.find(g => g.key === 'paypal')"
              >
                <RadioGroupItem id="paypal" value="paypal" />
                <Label for="paypal" class="flex flex-1 cursor-pointer items-center justify-between">
                  <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-100 dark:bg-yellow-900/30">
                      <CreditCard class="h-5 w-5 text-yellow-600 dark:text-yellow-400" />
                    </div>
                    <div>
                      <p class="font-semibold text-gray-900 dark:text-white">PayPal</p>
                      <p class="text-xs text-gray-600 dark:text-gray-400">PayPal account</p>
                    </div>
                  </div>
                  <Badge v-if="selectedGateway === 'paypal'" variant="default" class="text-xs">Selected</Badge>
                </Label>
              </div>
            </RadioGroup>
          </div>

          <!-- Guest fields -->
          <div v-if="!props.userToken" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="space-y-2">
              <Label>Billing Email</Label>
              <input
                type="email"
                placeholder="you@example.com"
                class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 dark:bg-gray-800 dark:text-white dark:border-gray-700"
                v-model="billingEmail"
              />
            </div>
            <div class="space-y-2">
              <Label>Full Name</Label>
              <input
                type="text"
                placeholder="John Doe"
                class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 dark:bg-gray-800 dark:text-white dark:border-gray-700"
                v-model="billingName"
              />
            </div>
            <p class="sm:col-span-2 text-xs text-gray-600 dark:text-gray-400">
              Preview totals require authentication. Any applicable tax is calculated by the payment provider at checkout. You can still checkout by providing your email and name.
            </p>
          </div>
        </CardContent>

        <CardFooter class="flex items-center justify-between">
          <div class="text-sm text-gray-600 dark:text-gray-400">
            {{ selectedProduct?.title ? 'Ready to purchase' : 'Select a product to continue' }}
          </div>
          <Button :disabled="loading || !selectedPriceId || redirecting" size="lg" @click="handleCheckout">
            <Loader2 v-if="redirecting" class="mr-2 h-4 w-4 animate-spin" />
            <span v-if="redirecting">Redirecting…</span>
            <span v-else>Continue to Payment</span>
          </Button>
        </CardFooter>
      </Card>
    </div>
  </div>
</template>