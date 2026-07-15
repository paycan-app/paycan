<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

const props = defineProps<{
  orderId?: string | number;
  apiBaseUrl: string;
  clientUrl?: string;
  cancelled?: boolean;
  error?: string | null;
}>();
</script>

<template>
  <Head title="Payment Cancelled" />
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center p-4 sm:p-6">
    <div class="w-full max-w-2xl">
      <!-- Cancel Card -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Cancel Header with SVG -->
        <div class="bg-gradient-to-br from-orange-50 to-amber-50 dark:from-orange-950 dark:to-amber-950 px-6 py-8 sm:px-8 sm:py-12 text-center border-b border-gray-200 dark:border-gray-700">
          <!-- Cancel SVG Illustration -->
          <div class="flex justify-center mb-6">
            <svg class="w-24 h-24 sm:w-32 sm:h-32" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
              <!-- Outer circle -->
              <circle cx="60" cy="60" r="58" class="fill-orange-100 dark:fill-orange-900/30" />
              <circle cx="60" cy="60" r="50" class="fill-orange-500 dark:fill-orange-600" />

              <!-- X mark -->
              <path d="M40 40 L80 80 M80 40 L40 80" stroke="white" stroke-width="6" stroke-linecap="round" class="animate-[draw_0.5s_ease-in-out]" />

              <!-- Decoration effects -->
              <circle cx="25" cy="35" r="3" class="fill-orange-400 dark:fill-orange-500 animate-pulse" />
              <circle cx="90" cy="30" r="2" class="fill-orange-400 dark:fill-orange-500 animate-pulse" style="animation-delay: 0.2s" />
              <circle cx="95" cy="75" r="3" class="fill-orange-400 dark:fill-orange-500 animate-pulse" style="animation-delay: 0.4s" />
              <circle cx="30" cy="85" r="2" class="fill-orange-400 dark:fill-orange-500 animate-pulse" style="animation-delay: 0.3s" />
            </svg>
          </div>

          <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white mb-2">
            Payment Cancelled
          </h1>
          <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300">
            {{ props.error ? props.error : 'Your payment was cancelled. No charges were made.' }}
          </p>
        </div>

        <!-- Content -->
        <div class="px-6 py-6 sm:px-8 sm:py-8">
          <div v-if="props.cancelled" class="space-y-4">
            <!-- Success cancellation message -->
            <div class="bg-green-50 dark:bg-green-950/30 border border-green-200 dark:border-green-900 rounded-lg p-4">
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-green-600 dark:text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                  <p class="text-sm font-medium text-green-800 dark:text-green-400">
                    Order successfully cancelled
                  </p>
                  <p class="text-xs text-green-700 dark:text-green-500 mt-1">
                    Your order <span v-if="props.orderId" class="font-semibold">#{{ props.orderId }}</span> has been cancelled and no payment was processed.
                  </p>
                </div>
              </div>
            </div>
          </div>

          <div v-else-if="!props.error" class="space-y-4">
            <!-- General cancellation info -->
            <div class="bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900 rounded-lg p-4">
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                  <p class="text-sm font-medium text-blue-800 dark:text-blue-400">
                    What happened?
                  </p>
                  <p class="text-xs text-blue-700 dark:text-blue-500 mt-1">
                    The payment process was cancelled before completion. You can try again or choose a different payment method.
                  </p>
                </div>
              </div>
            </div>

            <div v-if="props.orderId" class="text-center py-4">
              <p class="text-sm text-gray-600 dark:text-gray-400">
                Order Reference: <span class="font-semibold text-gray-900 dark:text-white">#{{ props.orderId }}</span>
              </p>
            </div>
          </div>

          <div v-else class="space-y-4">
            <!-- Error state -->
            <div class="bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900 rounded-lg p-4">
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-600 dark:text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                  <p class="text-sm font-medium text-red-800 dark:text-red-400">
                    {{ props.error }}
                  </p>
                  <p class="text-xs text-red-700 dark:text-red-500 mt-1">
                    If you believe this is an error, please contact support.
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- What's next section -->
          <div class="mt-8 bg-gray-50 dark:bg-gray-900/50 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">What would you like to do?</h3>
            <div class="space-y-3">
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <div class="flex-1">
                  <p class="text-sm text-gray-700 dark:text-gray-300">Review your order and try again</p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <div class="flex-1">
                  <p class="text-sm text-gray-700 dark:text-gray-300">Choose a different payment method</p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <div class="flex-1">
                  <p class="text-sm text-gray-700 dark:text-gray-300">Contact support if you need assistance</p>
                </div>
              </div>
            </div>
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
        <p class="text-xs text-gray-500 dark:text-gray-400">
          <!-- 
          Need help? Contact us at
          <a href="mailto:support@example.com" class="text-orange-600 dark:text-orange-500 hover:underline">support@example.com</a>
           -->
        </p>
      </div>
    </div>
  </div>
</template>