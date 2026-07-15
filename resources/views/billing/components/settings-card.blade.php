<div x-data="{ 
    activeCard: null,
    paymentMethod: 'card',
    billingCycle: 'monthly',
    autoRenew: true,
    notifications: {
        payment: true,
        invoice: true,
        usage: false
    }
}">
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Payment Method Card -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-6 hover-lift cursor-pointer"
             @click="activeCard = activeCard === 'payment' ? null : 'payment'"
             :class="activeCard === 'payment' ? 'ring-2 ring-blue-500' : ''">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="bg-blue-600 rounded-lg p-3 mr-4">
                        <i class="fas fa-credit-card text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Payment Method</h3>
                        <p class="text-sm text-gray-600">Manage your payment options</p>
                    </div>
                </div>
                <i class="fas fa-chevron-down transition-transform" 
                   :class="activeCard === 'payment' ? 'rotate-180' : ''"></i>
            </div>
            
            <div x-show="activeCard === 'payment'" x-transition class="space-y-4">
                <div class="space-y-3">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="radio" x-model="paymentMethod" value="card" class="text-blue-600">
                        <div class="flex items-center space-x-2">
                            <i class="fab fa-cc-visa text-blue-600"></i>
                            <span>Credit/Debit Card</span>
                        </div>
                    </label>
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="radio" x-model="paymentMethod" value="paypal" class="text-blue-600">
                        <div class="flex items-center space-x-2">
                            <i class="fab fa-paypal text-blue-600"></i>
                            <span>PayPal</span>
                        </div>
                    </label>
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="radio" x-model="paymentMethod" value="bank" class="text-blue-600">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-university text-blue-600"></i>
                            <span>Bank Transfer</span>
                        </div>
                    </label>
                </div>
                
                <div x-show="paymentMethod === 'card'" class="bg-white rounded-lg p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium">•••• •••• •••• 4242</span>
                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Active</span>
                    </div>
                    <button class="text-blue-600 text-sm hover:underline">Update Card</button>
                </div>
            </div>
        </div>

        <!-- Billing Cycle Card -->
        <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl p-6 hover-lift cursor-pointer"
             @click="activeCard = activeCard === 'billing' ? null : 'billing'"
             :class="activeCard === 'billing' ? 'ring-2 ring-green-500' : ''">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="bg-green-600 rounded-lg p-3 mr-4">
                        <i class="fas fa-calendar-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Billing Cycle</h3>
                        <p class="text-sm text-gray-600">Choose your billing frequency</p>
                    </div>
                </div>
                <i class="fas fa-chevron-down transition-transform" 
                   :class="activeCard === 'billing' ? 'rotate-180' : ''"></i>
            </div>
            
            <div x-show="activeCard === 'billing'" x-transition class="space-y-4">
                <div class="space-y-3">
                    <label class="flex items-center justify-between cursor-pointer p-3 bg-white rounded-lg">
                        <div class="flex items-center space-x-3">
                            <input type="radio" x-model="billingCycle" value="monthly" class="text-green-600">
                            <span>Monthly</span>
                        </div>
                        <span class="text-sm text-gray-600">$29/month</span>
                    </label>
                    <label class="flex items-center justify-between cursor-pointer p-3 bg-white rounded-lg">
                        <div class="flex items-center space-x-3">
                            <input type="radio" x-model="billingCycle" value="yearly" class="text-green-600">
                            <span>Yearly</span>
                        </div>
                        <div class="text-right">
                            <span class="text-sm text-gray-600">$290/year</span>
                            <span class="block text-xs text-green-600">Save 17%</span>
                        </div>
                    </label>
                </div>
                
                <div class="flex items-center space-x-3 p-3 bg-white rounded-lg">
                    <input type="checkbox" x-model="autoRenew" class="text-green-600">
                    <span class="text-sm">Auto-renew subscription</span>
                </div>
            </div>
        </div>

        <!-- Notifications Card -->
        <div class="bg-gradient-to-br from-purple-50 to-violet-100 rounded-xl p-6 hover-lift cursor-pointer"
             @click="activeCard = activeCard === 'notifications' ? null : 'notifications'"
             :class="activeCard === 'notifications' ? 'ring-2 ring-purple-500' : ''">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="bg-purple-600 rounded-lg p-3 mr-4">
                        <i class="fas fa-bell text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Notifications</h3>
                        <p class="text-sm text-gray-600">Manage your alerts</p>
                    </div>
                </div>
                <i class="fas fa-chevron-down transition-transform" 
                   :class="activeCard === 'notifications' ? 'rotate-180' : ''"></i>
            </div>
            
            <div x-show="activeCard === 'notifications'" x-transition class="space-y-4">
                <div class="space-y-3">
                    <label class="flex items-center justify-between cursor-pointer p-3 bg-white rounded-lg">
                        <span class="text-sm">Payment confirmations</span>
                        <input type="checkbox" x-model="notifications.payment" class="text-purple-600">
                    </label>
                    <label class="flex items-center justify-between cursor-pointer p-3 bg-white rounded-lg">
                        <span class="text-sm">Invoice notifications</span>
                        <input type="checkbox" x-model="notifications.invoice" class="text-purple-600">
                    </label>
                    <label class="flex items-center justify-between cursor-pointer p-3 bg-white rounded-lg">
                        <span class="text-sm">Usage alerts</span>
                        <input type="checkbox" x-model="notifications.usage" class="text-purple-600">
                    </label>
                </div>
            </div>
        </div>

        <!-- Billing History Card -->
        <div class="bg-gradient-to-br from-orange-50 to-amber-100 rounded-xl p-6 hover-lift cursor-pointer"
             @click="activeCard = activeCard === 'history' ? null : 'history'"
             :class="activeCard === 'history' ? 'ring-2 ring-orange-500' : ''">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="bg-orange-600 rounded-lg p-3 mr-4">
                        <i class="fas fa-history text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Billing History</h3>
                        <p class="text-sm text-gray-600">View past transactions</p>
                    </div>
                </div>
                <i class="fas fa-chevron-down transition-transform" 
                   :class="activeCard === 'history' ? 'rotate-180' : ''"></i>
            </div>
            
            <div x-show="activeCard === 'history'" x-transition class="space-y-3">
                <div class="bg-white rounded-lg p-3 flex items-center justify-between">
                    <div>
                        <div class="font-medium text-sm">Dec 2024</div>
                        <div class="text-xs text-gray-600">Pro Plan</div>
                    </div>
                    <div class="text-right">
                        <div class="font-medium text-sm">$29.00</div>
                        <div class="text-xs text-green-600">Paid</div>
                    </div>
                </div>
                <div class="bg-white rounded-lg p-3 flex items-center justify-between">
                    <div>
                        <div class="font-medium text-sm">Nov 2024</div>
                        <div class="text-xs text-gray-600">Pro Plan</div>
                    </div>
                    <div class="text-right">
                        <div class="font-medium text-sm">$29.00</div>
                        <div class="text-xs text-green-600">Paid</div>
                    </div>
                </div>
                <button class="text-orange-600 text-sm hover:underline w-full text-center">View All</button>
            </div>
        </div>

        <!-- Usage & Limits Card -->
        <div class="bg-gradient-to-br from-teal-50 to-cyan-100 rounded-xl p-6 hover-lift cursor-pointer"
             @click="activeCard = activeCard === 'usage' ? null : 'usage'"
             :class="activeCard === 'usage' ? 'ring-2 ring-teal-500' : ''">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="bg-teal-600 rounded-lg p-3 mr-4">
                        <i class="fas fa-chart-bar text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Usage & Limits</h3>
                        <p class="text-sm text-gray-600">Monitor your consumption</p>
                    </div>
                </div>
                <i class="fas fa-chevron-down transition-transform" 
                   :class="activeCard === 'usage' ? 'rotate-180' : ''"></i>
            </div>
            
            <div x-show="activeCard === 'usage'" x-transition class="space-y-4">
                <div class="bg-white rounded-lg p-4 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span>API Calls</span>
                        <span>7,500 / 10,000</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-teal-600 h-2 rounded-full" style="width: 75%"></div>
                    </div>
                </div>
                <div class="bg-white rounded-lg p-4 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span>Storage</span>
                        <span>2.1 GB / 5 GB</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-teal-600 h-2 rounded-full" style="width: 42%"></div>
                    </div>
                </div>
                <button class="text-teal-600 text-sm hover:underline w-full text-center">Upgrade Plan</button>
            </div>
        </div>

        <!-- Account Settings Card -->
        <div class="bg-gradient-to-br from-red-50 to-pink-100 rounded-xl p-6 hover-lift cursor-pointer"
             @click="activeCard = activeCard === 'account' ? null : 'account'"
             :class="activeCard === 'account' ? 'ring-2 ring-red-500' : ''">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="bg-red-600 rounded-lg p-3 mr-4">
                        <i class="fas fa-user-cog text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Account Settings</h3>
                        <p class="text-sm text-gray-600">Manage your account</p>
                    </div>
                </div>
                <i class="fas fa-chevron-down transition-transform" 
                   :class="activeCard === 'account' ? 'rotate-180' : ''"></i>
            </div>
            
            <div x-show="activeCard === 'account'" x-transition class="space-y-3">
                <button class="w-full text-left p-3 bg-white rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <span class="text-sm">Download Invoice</span>
                        <i class="fas fa-download text-gray-400"></i>
                    </div>
                </button>
                <button class="w-full text-left p-3 bg-white rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <span class="text-sm">Update Billing Address</span>
                        <i class="fas fa-edit text-gray-400"></i>
                    </div>
                </button>
                <button class="w-full text-left p-3 bg-white rounded-lg hover:bg-red-50 transition-colors text-red-600">
                    <div class="flex items-center justify-between">
                        <span class="text-sm">Cancel Subscription</span>
                        <i class="fas fa-times text-red-400"></i>
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <div class="mt-8 flex justify-end">
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-medium transition-colors flex items-center space-x-2">
            <i class="fas fa-save"></i>
            <span>Save Changes</span>
        </button>
    </div>
</div>