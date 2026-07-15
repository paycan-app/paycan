<script setup lang="ts">
import { onMounted, ref } from 'vue';
import type { PayCan } from '@paycan/sdk';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Loader2, CreditCard, X, Play, RefreshCw } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

interface Props {
    apiClient: PayCan;
}

const props = defineProps<Props>();

const subscriptions = ref<any[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);

onMounted(async () => {
    await loadSubscriptions();
});

const loadSubscriptions = async () => {
    loading.value = true;
    error.value = null;
    try {
        const response = await props.apiClient.subscriptions.list({
            include: 'productPrice.product',
            sort: '-created_at',
        });
        subscriptions.value = response.data || [];
    } catch (err) {
        console.error('Failed to fetch subscriptions:', err);
        error.value = 'Failed to load subscriptions. Please try again.';
    } finally {
        loading.value = false;
    }
};

const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        active: 'bg-green-500/10 text-green-700 dark:text-green-400 border-green-500/20',
        cancelled: 'bg-gray-500/10 text-gray-700 dark:text-gray-400 border-gray-500/20',
        past_due: 'bg-red-500/10 text-red-700 dark:text-red-400 border-red-500/20',
        trialing: 'bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-500/20',
        paused: 'bg-yellow-500/10 text-yellow-700 dark:text-yellow-400 border-yellow-500/20',
        // Map backend's 'canceled' spelling
        canceled: 'bg-gray-500/10 text-gray-700 dark:text-gray-400 border-gray-500/20',
    };
    return colors[status] || 'bg-gray-500/10 text-gray-700 dark:text-gray-400 border-gray-500/20';
};

const formatPrice = (amount: number, currency: string) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency || 'USD',
    }).format(amount);
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

// Decide the correct period-end date to display
const getPeriodEndDate = (subscription: any): string | null => {
    if (subscription.status === 'active') {
        return subscription.next_billing_date || null;
    }
    return subscription.ends_at || null;
};

// Compute resume ability client-side for immediate correctness
const canResume = (subscription: any): boolean => {
    if (subscription.status !== 'canceled') return false;
    if (!subscription.ends_at) return false;
    return new Date(subscription.ends_at).getTime() > Date.now();
};

const handleCancel = async (subscriptionId: string) => {
    if (!confirm('Are you sure you want to cancel this subscription?')) {
        return;
    }

    try {
        await props.apiClient.subscriptions.cancel(subscriptionId);
        await loadSubscriptions();
        toast.success('Subscription cancelled successfully');
    } catch (err) {
        console.error('Failed to cancel subscription:', err);
        toast.error('Failed to cancel subscription. Please try again.');
    }
};

const handleResume = async (subscriptionId: string) => {
    try {
        await props.apiClient.subscriptions.resume(subscriptionId);
        await loadSubscriptions();
        toast.success('Subscription resumed successfully');
    } catch (err) {
        console.error('Failed to resume subscription:', err);
        toast.error('Failed to resume subscription. Please try again.');
    }
};

const handleManagePayment = async () => {
    try {
        const { url } = await props.apiClient.checkout.getPortalUrl();
        if (url) {
            if (window.parent !== window) {
                window.parent.location.href = url;
            } else {
                window.location.href = url;
            }
        }
    } catch (err) {
        console.error('Failed to get portal URL:', err);
        toast.error('Failed to access payment management. Please try again.');
    }
};
</script>

<template>
    <div>
        <!-- Loading State -->
        <div v-if="loading" class="flex items-center justify-center py-20">
            <Loader2 class="h-8 w-8 animate-spin text-primary" />
        </div>

        <!-- Empty State -->
        <div v-else-if="subscriptions.length === 0" class="py-20 text-center">
            <RefreshCw class="mx-auto mb-4 h-16 w-16 text-gray-400" />
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">No subscriptions</h3>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                You don't have any active subscriptions yet.
            </p>
        </div>

        <!-- Subscriptions List -->
        <div v-else class="space-y-4">
            <Card
                v-for="subscription in subscriptions"
                :key="subscription.id"
                class="transition-all duration-200 hover:shadow-lg"
            >
                <CardHeader>
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <CardTitle class="text-xl">
                                {{ subscription.product_price?.product?.title || 'Subscription' }}
                            </CardTitle>
                            <CardDescription class="mt-1">
                                {{ subscription.product_price?.title || 'Plan' }}
                            </CardDescription>
                        </div>
                        <Badge :class="getStatusColor(subscription.status)" class="border">
                            {{ subscription.status }}
                        </Badge>
                    </div>
                </CardHeader>

                <CardContent>
                    <div class="space-y-4">
                        <!-- Subscription Details -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Price</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ formatPrice(parseFloat(subscription.product_price?.amount || 0), subscription.product_price?.currency || 'USD') }}
                                    / {{ subscription.product_price?.billing_period }}
                                </p>
                            </div>
                            <div v-if="getPeriodEndDate(subscription)">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ subscription.status === 'active' ? 'Next Billing Date' : 'Ends On' }}
                                </p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ formatDate(getPeriodEndDate(subscription)!) }}
                                </p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-wrap gap-2">
                            <Button
                                @click="handleManagePayment"
                                variant="default"
                                size="sm"
                            >
                                <CreditCard class="mr-2 h-4 w-4" />
                                Manage Payment
                            </Button>

                            <Button
                                v-if="subscription.status === 'active'"
                                @click="handleCancel(subscription.id)"
                                variant="outline"
                                size="sm"
                            >
                                <X class="mr-2 h-4 w-4" />
                                Cancel Subscription
                            </Button>

                            <Button
                                v-if="subscription.status === 'canceled' && canResume(subscription)"
                                @click="handleResume(subscription.id)"
                                variant="outline"
                                size="sm"
                            >
                                <Play class="mr-2 h-4 w-4" />
                                Resume Subscription
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
