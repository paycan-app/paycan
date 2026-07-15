<script setup lang="ts">
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { PayCan } from '@paycan/sdk';
import ProductsView from '@/components/Portal/ProductsView.vue';
import OrdersView from '@/components/Portal/OrdersView.vue';
import SubscriptionsView from '@/components/Portal/SubscriptionsView.vue';
import { Button } from '@/components/ui/button';
import { Toaster } from '@/components/ui/sonner';
import { ShoppingBag, FileText, RefreshCw } from 'lucide-vue-next';

const props = defineProps<{
    userToken: string;
    apiBaseUrl: string;
}>();

const activeTab = ref('products');

// Initialize SDK with API URL (no apiKey - that's for admin only)
const apiClient = new PayCan({
    apiUrl: props.apiBaseUrl,
});

// Set the user token (JWT for user authentication)
apiClient.setUserToken(props.userToken);
</script>

<template>
    <Head title="Payment Portal" />

    <Toaster position="top-right" />

    <div class="portal-app min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6 dark:from-gray-900 dark:to-gray-950">
        <div class="mx-auto max-w-7xl">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white">
                    Payment Portal
                </h1>
                <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
                    Manage your purchases, subscriptions, and orders
                </p>
            </div>

            <!-- Navigation -->
            <div class="mb-6 flex flex-wrap gap-2">
                <Button
                    @click="activeTab = 'products'"
                    :variant="activeTab === 'products' ? 'default' : 'outline'"
                    size="lg"
                    class="text-lg"
                >
                    <ShoppingBag class="mr-2 h-5 w-5" />
                    Products
                </Button>
                <Button
                    @click="activeTab = 'orders'"
                    :variant="activeTab === 'orders' ? 'default' : 'outline'"
                    size="lg"
                    class="text-lg"
                >
                    <FileText class="mr-2 h-5 w-5" />
                    Orders
                </Button>
                <Button
                    @click="activeTab = 'subscriptions'"
                    :variant="activeTab === 'subscriptions' ? 'default' : 'outline'"
                    size="lg"
                    class="text-lg"
                >
                    <RefreshCw class="mr-2 h-5 w-5" />
                    Subscriptions
                </Button>
            </div>

            <!-- Content -->
            <div>
                <ProductsView v-if="activeTab === 'products'" :api-client="apiClient" />
                <OrdersView v-if="activeTab === 'orders'" :api-client="apiClient" />
                <SubscriptionsView v-if="activeTab === 'subscriptions'" :api-client="apiClient" />
            </div>
        </div>
    </div>
</template>
