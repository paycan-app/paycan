<script setup lang="ts">
import { onMounted, ref } from 'vue';
import type { PayCan } from '@paycan/sdk';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Loader2, ShoppingCart, Check } from 'lucide-vue-next';
import { toast } from 'vue-sonner';
import CheckoutModal from './CheckoutModal.vue';

interface Props {
    apiClient: PayCan;
}

const props = defineProps<Props>();

const products = ref<any[]>([]);
const loading = ref(true);
const checkoutModalOpen = ref(false);
const selectedProduct = ref<any>(null);
const selectedPrice = ref<any>(null);
const checkoutLoading = ref(false);

onMounted(async () => {
    try {
        const response = await props.apiClient.products.list({
            include: 'prices',
            per_page: 100, // Get all products
        });

        // Handle both paginated and non-paginated responses
        if (response.data) {
            products.value = response.data;
        } else if (Array.isArray(response)) {
            products.value = response;
        } else {
            products.value = [];
        }

        console.log('Loaded products:', products.value);
    } catch (error) {
        console.error('Failed to fetch products:', error);
        toast.error('Failed to load products');
    } finally {
        loading.value = false;
    }
});

const getProductTypeColor = (type: string) => {
    const colors: Record<string, string> = {
        digital: 'bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-500/20',
        physical: 'bg-green-500/10 text-green-700 dark:text-green-400 border-green-500/20',
        service: 'bg-purple-500/10 text-purple-700 dark:text-purple-400 border-purple-500/20',
        subscription: 'bg-orange-500/10 text-orange-700 dark:text-orange-400 border-orange-500/20',
    };
    return colors[type] || 'bg-gray-500/10 text-gray-700 dark:text-gray-400 border-gray-500/20';
};

const formatPrice = (amount: number, currency: string) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency || 'USD',
    }).format(amount);
};

const openCheckoutModal = (product: any, price: any) => {
    selectedProduct.value = product;
    selectedPrice.value = price;
    checkoutModalOpen.value = true;
};

const handleCheckout = async (gateway: 'stripe' | 'paypal') => {
    checkoutLoading.value = true;
    try {
        const response = await props.apiClient.checkout.create({
            product_id: selectedProduct.value.id,
            product_price_id: selectedPrice.value.id,
            gateway,
        });

        const checkoutUrl = response.checkout_url;

        // Check if the URL is an error URL
        if (checkoutUrl && checkoutUrl.includes('/payment/error')) {
            checkoutLoading.value = false;
            checkoutModalOpen.value = false;
            toast.error(
                `Payment gateway not configured. Please configure ${gateway === 'stripe' ? 'Stripe' : 'PayPal'} in the admin settings to enable payments.`,
                { duration: 6000 }
            );
            return;
        }

        if (checkoutUrl) {
            toast.success('Redirecting to checkout...');
            if (window.parent !== window) {
                window.parent.location.href = checkoutUrl;
            } else {
                window.location.href = checkoutUrl;
            }
        } else {
            throw new Error('No checkout URL received');
        }
    } catch (error: any) {
        console.error('Purchase error:', error);

        // Show more detailed error message
        let errorMessage = 'Failed to process purchase. Please try again.';
        if (error?.message) {
            errorMessage = error.message;
        } else if (error?.response?.data?.message) {
            errorMessage = error.response.data.message;
        } else if (error?.response?.data?.error) {
            errorMessage = error.response.data.error;
        }

        checkoutLoading.value = false;
        checkoutModalOpen.value = false;
        toast.error(errorMessage, { duration: 5000 });
    }
};
</script>

<template>
    <div class="products-view">
        <!-- Checkout Modal -->
        <CheckoutModal
            v-model:open="checkoutModalOpen"
            :product="selectedProduct"
            :price="selectedPrice"
            :loading="checkoutLoading"
            :api-client="apiClient"
            @checkout="handleCheckout"
        />

        <!-- Loading State -->
        <div v-if="loading" class="flex items-center justify-center py-20">
            <Card class="p-12 text-center">
                <Loader2 class="mx-auto mb-4 h-12 w-12 animate-spin text-primary" />
                <p class="text-lg text-gray-600 dark:text-gray-400">Loading products...</p>
            </Card>
        </div>

        <!-- Empty State -->
        <div v-else-if="products.length === 0" class="flex items-center justify-center py-20">
            <Card class="p-12 text-center">
                <ShoppingCart class="mx-auto mb-4 h-16 w-16 text-gray-400" />
                <h3 class="mb-2 text-xl font-semibold text-gray-900 dark:text-white">No Products Available</h3>
                <p class="text-gray-600 dark:text-gray-400">Check back later for new products.</p>
            </Card>
        </div>

        <!-- Products Grid -->
        <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <Card
                v-for="product in products"
                :key="product.id"
                class="flex flex-col overflow-hidden transition-all hover:shadow-lg"
            >
                <CardHeader class="space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <CardTitle class="text-2xl">{{ product.title }}</CardTitle>
                        <Badge :class="getProductTypeColor(product.type)" class="shrink-0">
                            {{ product.type }}
                        </Badge>
                    </div>
                    <CardDescription class="text-base">
                        {{ product.description || 'No description available' }}
                    </CardDescription>
                </CardHeader>

                <CardContent class="flex-1 space-y-4">
                    <div v-if="product.prices && product.prices.length > 0" class="space-y-3">
                        <div
                            v-for="price in product.prices"
                            :key="price.id"
                            class="flex items-center justify-between rounded-lg border border-gray-200 p-4 dark:border-gray-700"
                        >
                            <div class="space-y-1">
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ formatPrice(price.amount, price.currency) }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ price.billing_period ? `per ${price.billing_period}` : 'one-time' }}
                                </p>
                            </div>
                            <Button
                                @click="openCheckoutModal(product, price)"
                                size="lg"
                                class="gap-2"
                            >
                                <ShoppingCart class="h-5 w-5" />
                                Buy Now
                            </Button>
                        </div>
                    </div>
                    <p v-else class="text-sm text-gray-500 dark:text-gray-400">
                        No pricing available
                    </p>
                </CardContent>

                <CardFooter v-if="product.meta?.features && product.meta.features.length > 0" class="border-t pt-4">
                    <div class="w-full space-y-2">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Features:</p>
                        <ul class="space-y-1">
                            <li
                                v-for="(feature, index) in product.meta.features"
                                :key="index"
                                class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400"
                            >
                                <Check class="mt-0.5 h-4 w-4 shrink-0 text-green-500" />
                                <span>{{ feature }}</span>
                            </li>
                        </ul>
                    </div>
                </CardFooter>
            </Card>
        </div>
    </div>
</template>
