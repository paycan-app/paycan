<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Components Demo - SaaS Billing Solutions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-shadow {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="gradient-bg text-white py-16">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-4">SaaS Billing Components</h1>
            <p class="text-xl md:text-2xl opacity-90 mb-8">Modern, embeddable billing solutions for any SaaS platform</p>
            <div class="flex flex-wrap justify-center gap-4">
                <span class="bg-white/20 px-4 py-2 rounded-full text-sm">React Ready</span>
                <span class="bg-white/20 px-4 py-2 rounded-full text-sm">Vue Compatible</span>
                <span class="bg-white/20 px-4 py-2 rounded-full text-sm">Vanilla JS</span>
                <span class="bg-white/20 px-4 py-2 rounded-full text-sm">Responsive</span>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50" x-data="{ activeSection: 'billing-settings' }">
        <div class="container mx-auto px-6">
            <div class="flex space-x-8 overflow-x-auto py-4">
                <button @click="activeSection = 'billing-settings'; document.getElementById('billing-settings').scrollIntoView({behavior: 'smooth'})" 
                        :class="activeSection === 'billing-settings' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-blue-600'"
                        class="whitespace-nowrap pb-2 font-medium transition-colors">
                    Billing Settings
                </button>
                <button @click="activeSection = 'customer-portal'; document.getElementById('customer-portal').scrollIntoView({behavior: 'smooth'})" 
                        :class="activeSection === 'customer-portal' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-blue-600'"
                        class="whitespace-nowrap pb-2 font-medium transition-colors">
                    Customer Portal
                </button>
                <button @click="activeSection = 'use-cases'; document.getElementById('use-cases').scrollIntoView({behavior: 'smooth'})" 
                        :class="activeSection === 'use-cases' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-blue-600'"
                        class="whitespace-nowrap pb-2 font-medium transition-colors">
                    Use Cases
                </button>
                <button @click="activeSection = 'integration'; document.getElementById('integration').scrollIntoView({behavior: 'smooth'})" 
                        :class="activeSection === 'integration' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-blue-600'"
                        class="whitespace-nowrap pb-2 font-medium transition-colors">
                    Integration
                </button>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-12">
        <!-- Billing Settings Section -->
        <section id="billing-settings" class="mb-20">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Billing Settings Components</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">Three distinct layouts to match your application's design language and user experience requirements.</p>
            </div>

            <!-- Card-Based Layout -->
            <div class="mb-16">
                <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-credit-card text-blue-600 mr-3"></i>
                    Card-Based Layout
                </h3>
                <div class="bg-white rounded-xl card-shadow p-8">
                    @include('billing.components.settings-card')
                </div>
            </div>

            <!-- Sidebar Layout -->
            <div class="mb-16">
                <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-sidebar text-blue-600 mr-3"></i>
                    Sidebar Layout
                </h3>
                <div class="bg-white rounded-xl card-shadow overflow-hidden">
                    @include('billing.components.settings-sidebar')
                </div>
            </div>

            <!-- Tabbed Layout -->
            <div class="mb-16">
                <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-tabs text-blue-600 mr-3"></i>
                    Tabbed Layout
                </h3>
                <div class="bg-white rounded-xl card-shadow">
                    @include('billing.components.settings-tabbed')
                </div>
            </div>
        </section>

        <!-- Customer Portal Section -->
        <section id="customer-portal" class="mb-20">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Customer Portal Components</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">Self-service portals that empower your customers to manage their subscriptions and billing independently.</p>
            </div>

            <!-- Dashboard Layout -->
            <div class="mb-16">
                <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-tachometer-alt text-green-600 mr-3"></i>
                    Dashboard Layout
                </h3>
                <div class="bg-white rounded-xl card-shadow">
                    @include('billing.components.portal-dashboard')
                </div>
            </div>

            <!-- Minimal Layout -->
            <div class="mb-16">
                <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-minimize text-green-600 mr-3"></i>
                    Minimal Layout
                </h3>
                <div class="bg-white rounded-xl card-shadow">
                    @include('billing.components.portal-minimal')
                </div>
            </div>

            <!-- Enterprise Layout -->
            <div class="mb-16">
                <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-building text-green-600 mr-3"></i>
                    Enterprise Layout
                </h3>
                <div class="bg-white rounded-xl card-shadow">
                    @include('billing.components.portal-enterprise')
                </div>
            </div>
        </section>

        <!-- Use Cases Section -->
        <section id="use-cases" class="mb-20">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Use Cases & Examples</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">See how different types of SaaS businesses can implement these components.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                @include('billing.components.use-case-startup')
                @include('billing.components.use-case-enterprise')
                @include('billing.components.use-case-marketplace')
            </div>
        </section>

        <!-- Integration Section -->
        <section id="integration" class="mb-20">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Easy Integration</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">Copy and paste these components into your application with minimal setup required.</p>
            </div>

            @include('billing.components.integration-guide')
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-6 text-center">
            <h3 class="text-2xl font-bold mb-4">Ready to integrate?</h3>
            <p class="text-gray-400 mb-6">Choose the components that best fit your application and start building better billing experiences.</p>
            <div class="flex justify-center space-x-4">
                <button class="bg-blue-600 hover:bg-blue-700 px-6 py-3 rounded-lg font-medium transition-colors">
                    Download Components
                </button>
                <button class="border border-gray-600 hover:border-gray-500 px-6 py-3 rounded-lg font-medium transition-colors">
                    View Documentation
                </button>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling and active section tracking
        window.addEventListener('scroll', () => {
            const sections = ['billing-settings', 'customer-portal', 'use-cases', 'integration'];
            const scrollPos = window.scrollY + 100;
            
            sections.forEach(section => {
                const element = document.getElementById(section);
                if (element && scrollPos >= element.offsetTop && scrollPos < element.offsetTop + element.offsetHeight) {
                    // Update active section in Alpine.js
                    document.querySelector('[x-data]').__x.$data.activeSection = section;
                }
            });
        });
    </script>
</body>
</html>