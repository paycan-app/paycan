<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue';
import type { PayCan } from '@paycan/sdk';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { CreditCard, Loader2, ShoppingCart, Check, AlertCircle } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

interface Props {
    open: boolean;
    product: any;
    price: any;
    loading?: boolean;
    apiClient: PayCan;
}

interface Emits {
    (e: 'update:open', value: boolean): void;
    (e: 'checkout', gateway: 'stripe' | 'paypal'): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const selectedGateway = ref<'stripe' | 'paypal'>('stripe');
const previewData = ref<any>(null);
const previewLoading = ref(false);
const previewError = ref<string | null>(null);
const quantity = ref(1);

const formatPrice = (amount: number, currency: string) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency || 'USD',
    }).format(amount);
};

const billingPeriodText = computed(() => {
    if (!props.price?.billing_period) return 'one-time payment';
    if (props.price.billing_period === 'once') return 'one-time payment';
    return `per ${props.price.billing_period}`;
});

// Load preview when modal opens
const loadPreview = async () => {
    if (!props.open || !props.price?.id) return;

    previewLoading.value = true;
    previewError.value = null;

    try {
        const response = await props.apiClient.checkout.preview({
            product_price_id: props.price.id,
            quantity: quantity.value,
            gateway: selectedGateway.value,
        });

        previewData.value = response;
    } catch (error: any) {
        console.error('Failed to load preview:', error);
        previewError.value = error.message || 'Failed to load price preview';
    } finally {
        previewLoading.value = false;
    }
};

// Watch for changes that should trigger preview reload
watch([() => props.open, selectedGateway, quantity], () => {
    if (props.open) {
        loadPreview();
    }
});

// Load preview when modal opens
onMounted(() => {
    if (props.open) {
        loadPreview();
    }
});

const handleCheckout = () => {
    emit('checkout', selectedGateway.value);
};
</script>

<template>
    <Dialog :open="open" @update:open="(value) => emit('update:open', value)">
        <DialogContent class="max-w-md">
            <DialogHeader>
                <DialogTitle class="flex items-center gap-2 text-2xl">
                    <ShoppingCart class="h-6 w-6" />
                    Checkout
                </DialogTitle>
                <DialogDescription>
                    Review your purchase and select a payment method
                </DialogDescription>
            </DialogHeader>

            <div class="space-y-6 py-4">
                <!-- Product Summary -->
                <div class="space-y-4 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 space-y-1">
                            <h3 class="font-semibold text-gray-900 dark:text-white">
                                {{ product?.title }}
                            </h3>
                            <p v-if="product?.description" class="text-sm text-gray-600 dark:text-gray-400">
                                {{ product.description }}
                            </p>
                        </div>
                        <Badge
                            :class="{
                                'bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-500/20': product?.type === 'digital',
                                'bg-green-500/10 text-green-700 dark:text-green-400 border-green-500/20': product?.type === 'physical',
                                'bg-purple-500/10 text-purple-700 dark:text-purple-400 border-purple-500/20': product?.type === 'service',
                                'bg-orange-500/10 text-orange-700 dark:text-orange-400 border-orange-500/20': product?.type === 'subscription',
                            }"
                            class="shrink-0"
                        >
                            {{ product?.type }}
                        </Badge>
                    </div>

                    <!-- Features -->
                    <div v-if="product?.meta?.features && product.meta.features.length > 0" class="space-y-2">
                        <Separator class="my-3" />
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            What's included
                        </p>
                        <ul class="space-y-1.5">
                            <li
                                v-for="(feature, index) in product.meta.features.slice(0, 4)"
                                :key="index"
                                class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300"
                            >
                                <Check class="mt-0.5 h-4 w-4 shrink-0 text-green-600 dark:text-green-500" />
                                <span>{{ feature }}</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Price Breakdown with Preview -->
                    <Separator class="my-3" />

                    <div v-if="previewLoading" class="flex items-center justify-center py-4">
                        <Loader2 class="h-5 w-5 animate-spin text-primary" />
                        <span class="ml-2 text-sm text-gray-500">Calculating total...</span>
                    </div>

                    <div v-else-if="previewError" class="rounded-lg bg-red-50 p-3 dark:bg-red-900/20">
                        <div class="flex items-start gap-2">
                            <AlertCircle class="h-4 w-4 text-red-600 dark:text-red-400 mt-0.5 shrink-0" />
                            <p class="text-sm text-red-700 dark:text-red-300">{{ previewError }}</p>
                        </div>
                    </div>

                    <div v-else-if="previewData" class="space-y-2">
                        <!-- Subtotal -->
                        <div class="flex items-baseline justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ formatPrice(previewData.selected_price?.subtotal || price?.amount || 0, price?.currency || 'USD') }}
                            </span>
                        </div>

                        <!-- Total -->
                        <Separator class="my-2" />
                        <div class="flex items-baseline justify-between">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Total</span>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ formatPrice(previewData.selected_price?.final_price || price?.amount || 0, price?.currency || 'USD') }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ billingPeriodText }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div v-else class="flex items-baseline justify-between">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Total</span>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ formatPrice(price?.amount || 0, price?.currency || 'USD') }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ billingPeriodText }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Selection -->
                <div class="space-y-3">
                    <Label class="text-sm font-medium">Select Payment Method</Label>
                    <RadioGroup v-model="selectedGateway" class="space-y-2">
                        <!-- Stripe Option -->
                        <div
                            class="flex cursor-pointer items-center space-x-3 rounded-lg border-2 p-4 transition-all hover:bg-gray-50 dark:hover:bg-gray-800/50"
                            :class="
                                selectedGateway === 'stripe'
                                    ? 'border-primary bg-primary/5 dark:bg-primary/10'
                                    : 'border-gray-200 dark:border-gray-700'
                            "
                            @click="selectedGateway = 'stripe'"
                        >
                            <RadioGroupItem id="stripe" value="stripe" />
                            <Label
                                for="stripe"
                                class="flex flex-1 cursor-pointer items-center justify-between"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-900/30">
                                        <CreditCard class="h-5 w-5 text-indigo-600 dark:text-indigo-400" />
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">Stripe</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">
                                            Credit card, Apple Pay, Google Pay
                                        </p>
                                    </div>
                                </div>
                                <Badge v-if="selectedGateway === 'stripe'" variant="default" class="text-xs">
                                    Selected
                                </Badge>
                            </Label>
                        </div>

                        <!-- PayPal Option -->
                        <div
                            class="flex cursor-pointer items-center space-x-3 rounded-lg border-2 p-4 transition-all hover:bg-gray-50 dark:hover:bg-gray-800/50"
                            :class="
                                selectedGateway === 'paypal'
                                    ? 'border-primary bg-primary/5 dark:bg-primary/10'
                                    : 'border-gray-200 dark:border-gray-700'
                            "
                            @click="selectedGateway = 'paypal'"
                        >
                            <RadioGroupItem id="paypal" value="paypal" />
                            <Label
                                for="paypal"
                                class="flex flex-1 cursor-pointer items-center justify-between"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M20.067 8.478c.492.88.556 2.014.3 3.327-.74 3.806-3.276 5.12-6.514 5.12h-.5a.805.805 0 0 0-.794.68l-.04.22-.63 3.993-.032.17a.804.804 0 0 1-.794.679H7.72a.483.483 0 0 1-.477-.558L7.418 21h1.518l.95-6.02h1.385c4.678 0 7.75-2.203 8.796-6.502z"
                                                class="text-blue-600 dark:text-blue-400"
                                            />
                                            <path
                                                d="M7.629 3.073C8.02 2.41 8.928 2 10.126 2h5.613c.99 0 1.818.19 2.446.56.586.345 1.007.84 1.28 1.48.279.654.39 1.438.423 2.328v.04l.013.125c.042.419.042.846 0 1.265-.13 1.297-.444 2.422-1.133 3.197-.733.827-1.846 1.286-3.297 1.433l-.447.033c-.98.05-1.847.436-2.306 1.19l-.035.058a1.844 1.844 0 0 0-.216.987l-.007.127v.513l-.804 5.095a.483.483 0 0 1-.477.558H7.72a.483.483 0 0 1-.477-.558l1.386-8.798.01-.065a1.847 1.847 0 0 1 1.807-1.518h3.418c.99 0 1.847-.297 2.437-.82z"
                                                class="text-blue-700 dark:text-blue-500"
                                            />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">PayPal</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">
                                            Pay with your PayPal account
                                        </p>
                                    </div>
                                </div>
                                <Badge v-if="selectedGateway === 'paypal'" variant="default" class="text-xs">
                                    Selected
                                </Badge>
                            </Label>
                        </div>
                    </RadioGroup>
                </div>

                <!-- Security Notice -->
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/30">
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        🔒 Your payment information is secure and encrypted. We never store your card details.
                    </p>
                </div>
            </div>

            <DialogFooter class="gap-2 sm:gap-0">
                <Button
                    variant="outline"
                    @click="emit('update:open', false)"
                    :disabled="loading"
                >
                    Cancel
                </Button>
                <Button
                    @click="handleCheckout"
                    :disabled="loading"
                    class="gap-2"
                    size="lg"
                >
                    <Loader2 v-if="loading" class="h-4 w-4 animate-spin" />
                    <CreditCard v-else class="h-4 w-4" />
                    {{ loading ? 'Processing...' : `Pay ${formatPrice(price?.amount || 0, price?.currency || 'USD')}` }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
