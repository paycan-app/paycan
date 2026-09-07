<script setup lang="ts">
import { onMounted, ref, computed } from 'vue';
import type { PayCan } from '@paycan/sdk';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Loader2, Zap, Sparkles, Plus, History, ArrowUpRight, ArrowDownLeft } from 'lucide-vue-next';

interface Props {
    apiClient: PayCan;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'switch-tab', tab: string): void;
}>();

const wallets = ref<any[]>([]);
const transactions = ref<any[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);

onMounted(async () => {
    await loadWalletData();
});

const loadWalletData = async () => {
    loading.value = true;
    error.value = null;
    try {
        // Fetch wallets list
        const walletResponse = await props.apiClient.wallets.list();
        wallets.value = walletResponse.data || [];

        // Fetch recent transactions for the basic or default wallet
        const txResponse = await props.apiClient.wallets.transactions('basic', {
            per_page: 15,
        });
        transactions.value = (txResponse as any).data || [];
    } catch (err) {
        console.error('Failed to load wallet data:', err);
        error.value = 'Failed to load credit balances. Please try again.';
    } finally {
        loading.value = false;
    }
};

const basicWallet = computed(() => {
    return wallets.value.find((w) => w.type === 'basic') || {
        balance: 0,
        type: 'basic',
        currency: 'credits',
        is_active: true,
    };
});

const premiumWallet = computed(() => {
    return wallets.value.find((w) => w.type === 'premium') || {
        balance: 0,
        type: 'premium',
        currency: 'credits',
        is_active: true,
    };
});

const formatNumber = (val: number | string) => {
    const num = parseFloat(val as string) || 0;
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    }).format(num);
};

const formatDate = (dateStr: string) => {
    return new Date(dateStr).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getActionBadgeColor = (action: string, type: string) => {
    if (type === 'debit') {
        return 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/20';
    }
    if (action.includes('grant') || action.includes('renewal')) {
        return 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/20';
    }
    return 'bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-500/20';
};
</script>

<template>
    <div class="wallets-view space-y-8">
        <!-- Error alert -->
        <div v-if="error" class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700 dark:border-red-900/50 dark:bg-red-950/50 dark:text-red-400">
            <p>{{ error }}</p>
            <Button variant="outline" size="sm" class="mt-2" @click="loadWalletData">Retry</Button>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex min-h-[300px] flex-col items-center justify-center gap-3">
            <Loader2 class="h-8 w-8 animate-spin text-gray-500" />
            <p class="text-sm text-gray-500">Loading credit wallets...</p>
        </div>

        <div v-else class="space-y-8">
            <!-- Wallets Grid -->
            <div class="grid gap-6 md:grid-cols-2">
                <!-- Basic Credits Card -->
                <Card class="relative overflow-hidden border-2 border-blue-500/20 bg-gradient-to-br from-white to-blue-50/30 shadow-sm dark:from-gray-900 dark:to-blue-950/20">
                    <CardHeader class="pb-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="rounded-lg bg-blue-500/10 p-2 text-blue-600 dark:text-blue-400">
                                    <Zap class="h-5 w-5" />
                                </div>
                                <div>
                                    <CardTitle class="text-lg">Basic Credits</CardTitle>
                                    <CardDescription>Standard models & fast tasks</CardDescription>
                                </div>
                            </div>
                            <Badge variant="outline" class="bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300">
                                Active
                            </Badge>
                        </div>
                    </CardHeader>
                    <CardContent class="pt-4">
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                                {{ formatNumber(basicWallet.balance) }}
                            </span>
                            <span class="text-sm font-medium text-gray-500">credits</span>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            Available for daily agent runs, summarization, and lightweight queries.
                        </p>
                    </CardContent>
                </Card>

                <!-- Premium Credits Card -->
                <Card class="relative overflow-hidden border-2 border-purple-500/20 bg-gradient-to-br from-white to-purple-50/30 shadow-sm dark:from-gray-900 dark:to-purple-950/20">
                    <CardHeader class="pb-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="rounded-lg bg-purple-500/10 p-2 text-purple-600 dark:text-purple-400">
                                    <Sparkles class="h-5 w-5" />
                                </div>
                                <div>
                                    <CardTitle class="text-lg">Premium Credits</CardTitle>
                                    <CardDescription>Frontier & reasoning models</CardDescription>
                                </div>
                            </div>
                            <Badge variant="outline" class="bg-purple-50 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300">
                                High Priority
                            </Badge>
                        </div>
                    </CardHeader>
                    <CardContent class="pt-4">
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                                {{ formatNumber(premiumWallet.balance) }}
                            </span>
                            <span class="text-sm font-medium text-gray-500">credits</span>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            Required for advanced autonomous reasoning, deep research, and high-token runs.
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Action Banner -->
            <div class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Need more credits?</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Upgrade your subscription or purchase an instant credit pack.
                    </p>
                </div>
                <Button @click="emit('switch-tab', 'products')" class="gap-2">
                    <Plus class="h-4 w-4" />
                    Browse Plans & Packs
                </Button>
            </div>

            <!-- Activity Ledger -->
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <History class="h-5 w-5 text-gray-500" />
                            <CardTitle class="text-lg">Recent Credit Activity</CardTitle>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <div v-if="transactions.length === 0" class="py-8 text-center text-sm text-gray-500">
                        No credit activity recorded yet.
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b border-gray-200 text-xs uppercase text-gray-500 dark:border-gray-800">
                                <tr>
                                    <th class="py-3 px-2">Type</th>
                                    <th class="py-3 px-2">Description</th>
                                    <th class="py-3 px-2">Date</th>
                                    <th class="py-3 px-2 text-right">Amount</th>
                                    <th class="py-3 px-2 text-right">Balance After</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                <tr v-for="tx in transactions" :key="tx.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-800/50">
                                    <td class="py-3 px-2">
                                        <Badge variant="outline" :class="getActionBadgeColor(tx.action, tx.type)">
                                            <span class="flex items-center gap-1">
                                                <ArrowDownLeft v-if="tx.type === 'credit'" class="h-3 w-3" />
                                                <ArrowUpRight v-else class="h-3 w-3" />
                                                {{ tx.action.replace('_', ' ') }}
                                            </span>
                                        </Badge>
                                    </td>
                                    <td class="py-3 px-2 text-gray-700 dark:text-gray-300">
                                        {{ tx.description || 'Usage transaction' }}
                                    </td>
                                    <td class="py-3 px-2 text-xs text-gray-500">
                                        {{ formatDate(tx.created_at) }}
                                    </td>
                                    <td class="py-3 px-2 text-right font-medium" :class="tx.type === 'credit' ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-900 dark:text-gray-100'">
                                        {{ tx.type === 'credit' ? '+' : '-' }}{{ formatNumber(tx.amount) }}
                                    </td>
                                    <td class="py-3 px-2 text-right text-gray-500">
                                        {{ formatNumber(tx.balance_after) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
