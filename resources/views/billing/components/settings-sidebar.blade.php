<div x-data="{ 
    activeTab: 'payment',
    paymentMethod: 'card',
    billingCycle: 'monthly',
    autoRenew: true,
    notifications: {
        payment: true,
        invoice: true,
        usage: false
    }
}" class="flex min-h-[600px]">
    <!-- Sidebar Navigation -->
    <div class="w-64 bg-gray-50 border-r border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Billing Settings</h3>
        
        <nav class="space-y-2">
            <button @click="activeTab = 'payment'" 
                    :class="activeTab === 'payment' ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100'"
                    class="w-full flex items-center px-4 py-3 rounded-lg transition-colors text-left">
                <i class="fas fa-credit-card mr-3"></i>
                <span>Payment Method</span>
            </button>
            
            <button @click="activeTab = 'billing'" 
                    :class="activeTab === 'billing' ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100'"
                    class="w-full flex items-center px-4 py-3 rounded-lg transition-colors text-left">
                <i class="fas fa-calendar-alt mr-3"></i>
                <span>Billing Cycle</span>
            </button>
            
            <button @click="activeTab = 'notifications'" 
                    :class="activeTab === 'notifications' ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100'"
                    class="w-full flex items-center px-4 py-3 rounded-lg transition-colors text-left">
                <i class="fas fa-bell mr-3"></i>
                <span>Notifications</span>
            </button>
            
            <button @click="activeTab = 'history'" 
                    :class="activeTab === 'history' ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100'"
                    class="w-full flex items-center px-4 py-3 rounded-lg transition-colors text-left">
                <i class="fas fa-history mr-3"></i>
                <span>Billing History</span>
            </button>
            
            <button @click="activeTab = 'usage'" 
                    :class="activeTab === 'usage' ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100'"
                    class="w-full flex items-center px-4 py-3 rounded-lg transition-colors text-left">
                <i class="fas fa-chart-bar mr-3"></i>
                <span>Usage & Limits</span>
            </button>
            
            <button @click="activeTab = 'account'" 
                    :class="activeTab === 'account' ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100'"
                    class="w-full flex items-center px-4 py-3 rounded-lg transition-colors text-left">
                <i class="fas fa-user-cog mr-3"></i>
                <span>Account Settings</span>
            </button>
        </nav>
    </div>

    <!-- Main Content Area -->
    <div class="flex-1 p-8">
        <!-- Payment Method Tab -->
        <div x-show="activeTab === 'payment'" x-transition>
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Payment Method</h2>
                <p class="text-gray-600">Manage how you pay for your subscription</p>
            </div>

            <div class="space-y-6">
                <!-- Current Payment Method -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Current Payment Method</h3>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="bg-blue-600 rounded-lg p-3">
                                <i class="fab fa-cc-visa text-white text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium">•••• •••• •••• 4242</div>
                                <div class="text-sm text-gray-600">Expires 12/2025</div>
                            </div>
                        </div>
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                            Update
                        </button>
                    </div>
                </div>

                <!-- Payment Method Options -->
                <div class="space-y-4">
                    <h3 class="font-semibold text-gray-900">Payment Options</h3>
                    
                    <label class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" x-model="paymentMethod" value="card" class="text-blue-600">
                        <i class="fab fa-cc-visa text-blue-600 text-xl"></i>
                        <div>
                            <div class="font-medium">Credit/Debit Card</div>
                            <div class="text-sm text-gray-600">Visa, Mastercard, American Express</div>
                        </div>
                    </label>
                    
                    <label class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" x-model="paymentMethod" value="paypal" class="text-blue-600">
                        <i class="fab fa-paypal text-blue-600 text-xl"></i>
                        <div>
                            <div class="font-medium">PayPal</div>
                            <div class="text-sm text-gray-600">Pay with your PayPal account</div>
                        </div>
                    </label>
                    
                    <label class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" x-model="paymentMethod" value="bank" class="text-blue-600">
                        <i class="fas fa-university text-blue-600 text-xl"></i>
                        <div>
                            <div class="font-medium">Bank Transfer</div>
                            <div class="text-sm text-gray-600">Direct bank transfer (ACH)</div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Billing Cycle Tab -->
        <div x-show="activeTab === 'billing'" x-transition>
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Billing Cycle</h2>
                <p class="text-gray-600">Choose how often you want to be billed</p>
            </div>

            <div class="space-y-6">
                <!-- Current Plan -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <h3 class="font-semibold text-gray-900 mb-2">Current Plan</h3>
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-medium text-lg">Pro Plan</div>
                            <div class="text-sm text-gray-600">Next billing: January 15, 2025</div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-green-600">$29</div>
                            <div class="text-sm text-gray-600">per month</div>
                        </div>
                    </div>
                </div>

                <!-- Billing Frequency -->
                <div class="space-y-4">
                    <h3 class="font-semibold text-gray-900">Billing Frequency</h3>
                    
                    <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                        <div class="flex items-center space-x-4">
                            <input type="radio" x-model="billingCycle" value="monthly" class="text-green-600">
                            <div>
                                <div class="font-medium">Monthly</div>
                                <div class="text-sm text-gray-600">Billed every month</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold">$29</div>
                            <div class="text-sm text-gray-600">per month</div>
                        </div>
                    </label>
                    
                    <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                        <div class="flex items-center space-x-4">
                            <input type="radio" x-model="billingCycle" value="yearly" class="text-green-600">
                            <div>
                                <div class="font-medium">Yearly</div>
                                <div class="text-sm text-gray-600">Billed annually</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold">$290</div>
                            <div class="text-sm text-green-600">Save $58 (17%)</div>
                        </div>
                    </label>
                </div>

                <!-- Auto-renewal -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" x-model="autoRenew" class="text-green-600">
                        <div>
                            <div class="font-medium">Auto-renewal</div>
                            <div class="text-sm text-gray-600">Automatically renew your subscription</div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Notifications Tab -->
        <div x-show="activeTab === 'notifications'" x-transition>
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Notification Preferences</h2>
                <p class="text-gray-600">Choose what notifications you want to receive</p>
            </div>

            <div class="space-y-4">
                <div class="border border-gray-200 rounded-lg p-4">
                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <div class="font-medium">Payment confirmations</div>
                            <div class="text-sm text-gray-600">Get notified when payments are processed</div>
                        </div>
                        <input type="checkbox" x-model="notifications.payment" class="text-purple-600">
                    </label>
                </div>
                
                <div class="border border-gray-200 rounded-lg p-4">
                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <div class="font-medium">Invoice notifications</div>
                            <div class="text-sm text-gray-600">Receive invoices via email</div>
                        </div>
                        <input type="checkbox" x-model="notifications.invoice" class="text-purple-600">
                    </label>
                </div>
                
                <div class="border border-gray-200 rounded-lg p-4">
                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <div class="font-medium">Usage alerts</div>
                            <div class="text-sm text-gray-600">Get alerts when approaching limits</div>
                        </div>
                        <input type="checkbox" x-model="notifications.usage" class="text-purple-600">
                    </label>
                </div>
            </div>
        </div>

        <!-- Billing History Tab -->
        <div x-show="activeTab === 'history'" x-transition>
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Billing History</h2>
                <p class="text-gray-600">View and download your past invoices</p>
            </div>

            <div class="space-y-4">
                <div class="border border-gray-200 rounded-lg p-4 flex items-center justify-between">
                    <div>
                        <div class="font-medium">December 2024</div>
                        <div class="text-sm text-gray-600">Pro Plan - Monthly</div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="font-bold">$29.00</span>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Paid</span>
                        <button class="text-blue-600 hover:underline text-sm">Download</button>
                    </div>
                </div>
                
                <div class="border border-gray-200 rounded-lg p-4 flex items-center justify-between">
                    <div>
                        <div class="font-medium">November 2024</div>
                        <div class="text-sm text-gray-600">Pro Plan - Monthly</div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="font-bold">$29.00</span>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Paid</span>
                        <button class="text-blue-600 hover:underline text-sm">Download</button>
                    </div>
                </div>
                
                <div class="border border-gray-200 rounded-lg p-4 flex items-center justify-between">
                    <div>
                        <div class="font-medium">October 2024</div>
                        <div class="text-sm text-gray-600">Pro Plan - Monthly</div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="font-bold">$29.00</span>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Paid</span>
                        <button class="text-blue-600 hover:underline text-sm">Download</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Usage Tab -->
        <div x-show="activeTab === 'usage'" x-transition>
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Usage & Limits</h2>
                <p class="text-gray-600">Monitor your current usage and plan limits</p>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="border border-gray-200 rounded-lg p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">API Calls</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span>Used</span>
                            <span>7,500 / 10,000</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-teal-600 h-3 rounded-full" style="width: 75%"></div>
                        </div>
                        <div class="text-xs text-gray-600">Resets on January 15, 2025</div>
                    </div>
                </div>
                
                <div class="border border-gray-200 rounded-lg p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Storage</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span>Used</span>
                            <span>2.1 GB / 5 GB</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-teal-600 h-3 rounded-full" style="width: 42%"></div>
                        </div>
                        <div class="text-xs text-gray-600">Additional storage available</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Settings Tab -->
        <div x-show="activeTab === 'account'" x-transition>
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Account Settings</h2>
                <p class="text-gray-600">Manage your account and subscription</p>
            </div>

            <div class="space-y-4">
                <button class="w-full flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-download text-gray-400"></i>
                        <span>Download All Invoices</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </button>
                
                <button class="w-full flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-edit text-gray-400"></i>
                        <span>Update Billing Address</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </button>
                
                <button class="w-full flex items-center justify-between p-4 border border-red-200 rounded-lg hover:bg-red-50 transition-colors text-red-600">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-times text-red-400"></i>
                        <span>Cancel Subscription</span>
                    </div>
                    <i class="fas fa-chevron-right text-red-400"></i>
                </button>
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
</div>