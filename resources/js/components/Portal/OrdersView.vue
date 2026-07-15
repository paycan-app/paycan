<script setup lang="ts">
import { onMounted, ref } from 'vue';
import type { PayCan } from '@paycan/sdk';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Loader2, Download, Key, Package } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

interface Props {
    apiClient: PayCan;
}

const props = defineProps<Props>();

const orders = ref<any[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);

onMounted(async () => {
    try {
        const response = await props.apiClient.orders.list({
            include: 'productPrice.product',
            sort: '-created_at',
        });
        orders.value = response.data || [];
    } catch (err) {
        console.error('Failed to fetch orders:', err);
        error.value = 'Failed to load orders. Please try again.';
    } finally {
        loading.value = false;
    }
});

const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        paid: 'bg-green-500/10 text-green-700 dark:text-green-400 border-green-500/20',
        completed: 'bg-green-500/10 text-green-700 dark:text-green-400 border-green-500/20',
        pending: 'bg-yellow-500/10 text-yellow-700 dark:text-yellow-400 border-yellow-500/20',
        processing: 'bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-500/20',
        failed: 'bg-red-500/10 text-red-700 dark:text-red-400 border-red-500/20',
        cancelled: 'bg-gray-500/10 text-gray-700 dark:text-gray-400 border-gray-500/20',
        refunded: 'bg-purple-500/10 text-purple-700 dark:text-purple-400 border-purple-500/20',
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

const handleDownload = async (orderId: string) => {
    try {
        const { downloads } = await props.apiClient.orders.getDownloads(orderId);
        if (downloads && downloads.length > 0) {
            downloads.forEach((download: any) => {
                if (download.download_url) {
                    window.open(download.download_url, '_blank');
                }
            });
            toast.success('Opening downloads...');
        } else {
            toast.info('No downloads available for this order');
        }
    } catch (err) {
        console.error('Failed to get downloads:', err);
        toast.error('Failed to access downloads. Please try again.');
    }
};

const handleViewLicense = async (orderId: string) => {
    try {
        const { licenses } = await props.apiClient.orders.getLicenses(orderId);
        if (licenses && licenses.length > 0) {
            const licenseText = licenses
                .map((license: any) => `${license.product_title}: ${license.license_key}`)
                .join('\n\n');
            alert(`Your License Keys:\n\n${licenseText}`);
            toast.success('License keys displayed');
        } else {
            toast.info('No license keys available for this order');
        }
    } catch (err) {
        console.error('Failed to get licenses:', err);
        toast.error('Failed to access license keys. Please try again.');
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
        <div v-else-if="orders.length === 0" class="py-20 text-center">
            <Package class="mx-auto mb-4 h-16 w-16 text-gray-400" />
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">No orders yet</h3>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Your orders will appear here after purchase.</p>
        </div>

        <!-- Orders List -->
        <div v-else class="space-y-4">
            <Card
                v-for="order in orders"
                :key="order.id"
                class="transition-all duration-200 hover:shadow-lg"
            >
                <CardHeader>
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <CardTitle class="text-xl">
                                {{ order.product_price?.product?.title || 'Product' }}
                            </CardTitle>
                            <CardDescription class="mt-1">
                                Order #{{ order.order_number }} •
                                {{ formatDate(order.created_at) }}
                            </CardDescription>
                        </div>
                        <Badge :class="getStatusColor(order.status)" class="border">
                            {{ order.status }}
                        </Badge>
                    </div>
                </CardHeader>

                <CardContent>
                    <div class="space-y-4">
                        <!-- Order Details -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ formatPrice(parseFloat(order.total), order.currency) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Method</p>
                                <p class="text-lg font-semibold capitalize text-gray-900 dark:text-white">
                                    {{ order.gateway }}
                                </p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div v-if="order.status === 'completed' || order.status === 'paid'" class="flex gap-2">
                            <Button
                                v-if="order.product_price?.product?.type === 'digital'"
                                @click="handleDownload(order.id)"
                                variant="outline"
                                size="sm"
                            >
                                <Download class="mr-2 h-4 w-4" />
                                Download Files
                            </Button>
                            <Button
                                v-if="order.product_price?.product?.type === 'digital'"
                                @click="handleViewLicense(order.id)"
                                variant="outline"
                                size="sm"
                            >
                                <Key class="mr-2 h-4 w-4" />
                                View License
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
