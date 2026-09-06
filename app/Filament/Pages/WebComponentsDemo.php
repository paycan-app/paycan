<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WebComponentsDemo extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-code-bracket';

    protected static ?string $navigationLabel = 'Web Components Demo';

    protected static ?string $title = 'Web Components Demo';

    protected static ?int $navigationSort = 2;

    protected static \UnitEnum|string|null $navigationGroup = null;

    public $demoUser;

    public $token;

    public $productId;

    public $priceId;

    /**
     * Get the view for this page
     */
    public function getView(): string
    {
        return 'filament.pages.web-components-demo';
    }

    /**
     * Mount the page and prepare demo data
     */
    public function mount(): void
    {
        // Find or create demo user
        $this->demoUser = $this->getOrCreateDemoUser();

        // Generate fresh token for demo
        $this->token = $this->demoUser->createToken('web-components-demo-token')->plainTextToken;

        // Seed demo data if needed
        $this->seedDemoDataIfNeeded();

        // Get a sample product and price for demos
        $this->getSampleProductAndPrice();
    }

    /**
     * Get or create the demo user
     */
    protected function getOrCreateDemoUser(): User
    {
        // Find existing demo user by checking for users with emails starting with 'demo-'
        $demoUser = User::where('email', 'like', 'demo-%@paycan.test')
            ->where('name', 'Web Components Demo User')
            ->first();

        if (! $demoUser) {
            // Create new demo user with unpredictable ID
            $demoUser = User::factory()->create([
                'name' => 'Web Components Demo User',
                'email' => 'demo-'.Str::uuid().'@paycan.test',
                'password' => Hash::make(Str::random(32)), // Random secure password
            ]);
        }

        return $demoUser;
    }

    /**
     * Seed demo data for the user if they don't have any
     */
    protected function seedDemoDataIfNeeded(): void
    {
        $this->seedDemoOrdersIfNeeded();
        $this->seedDemoWalletsIfNeeded();
    }

    /**
     * Seed demo orders and subscriptions if needed
     */
    protected function seedDemoOrdersIfNeeded(): void
    {
        // Check if user already has orders
        if ($this->demoUser->orders()->count() > 0) {
            return;
        }

        // Get first active product for demo data
        $product = \App\Models\Product::where('is_active', true)
            ->whereHas('prices', function ($query) {
                $query->where('is_active', true);
            })
            ->with('prices')
            ->first();

        if (! $product) {
            return; // No products available, skip seeding
        }

        $price = $product->prices->first();

        // Create 2 completed orders with transactions
        for ($i = 0; $i < 2; $i++) {
            $order = \App\Models\Order::factory()->create([
                'user_id' => $this->demoUser->id,
                'product_price_id' => $price->id,
                'status' => 'completed',
                'gateway' => $i % 2 === 0 ? 'stripe' : 'paypal',
            ]);

            // Create transaction for the order
            \App\Models\Transaction::factory()->create([
                'user_id' => $this->demoUser->id,
                'order_id' => $order->id,
                'gateway' => $order->gateway,
                'status' => 'succeeded',
                'amount' => $order->total,
            ]);
        }

        // Create 1 active subscription with transaction
        $subscription = \App\Models\Subscription::factory()->create([
            'user_id' => $this->demoUser->id,
            'product_price_id' => $price->id,
            'status' => 'active',
            'gateway' => 'stripe',
        ]);

        // Create order and transaction for the subscription
        $subscriptionOrder = \App\Models\Order::factory()->create([
            'user_id' => $this->demoUser->id,
            'product_price_id' => $price->id,
            'subscription_id' => $subscription->id,
            'status' => 'completed',
            'gateway' => 'stripe',
        ]);

        \App\Models\Transaction::factory()->create([
            'user_id' => $this->demoUser->id,
            'order_id' => $subscriptionOrder->id,
            'subscription_id' => $subscription->id,
            'gateway' => 'stripe',
            'status' => 'succeeded',
            'amount' => $subscriptionOrder->total,
        ]);
    }

    /**
     * Seed demo wallets and credit transaction logs if needed
     */
    protected function seedDemoWalletsIfNeeded(): void
    {
        if ($this->demoUser->wallets()->count() > 0) {
            return;
        }

        $walletService = app(\App\Services\Wallet\WalletService::class);

        // 1. Initial subscription grant on basic wallet: 500 credits
        $walletService->addCredits(
            $this->demoUser,
            500.0,
            'basic',
            'subscription_grant',
            'Monthly subscription plan credits',
            'sub_demo_initial',
            ['plan' => 'Pro Monthly', 'tier' => 'standard']
        );

        // 2. Initial grant on premium wallet: 50 credits
        $walletService->addCredits(
            $this->demoUser,
            50.0,
            'premium',
            'subscription_grant',
            'Monthly premium AI fast-lane credits',
            'sub_demo_prem_initial',
            ['plan' => 'Pro Monthly', 'tier' => 'premium']
        );

        // 3. Deduct basic credits for AI usage demo
        $walletService->deductCredits(
            $this->demoUser,
            2.5,
            'basic',
            'AI Chat Completion - 1,250 tokens',
            'req_ai_98234a',
            ['model' => 'gpt-4o-mini', 'prompt_tokens' => 850, 'completion_tokens' => 400]
        );

        // 4. Deduct second basic usage
        $walletService->deductCredits(
            $this->demoUser,
            5.0,
            'basic',
            'AI Document Analysis - 2,500 tokens',
            'req_ai_10928b',
            ['model' => 'gpt-4o-mini', 'prompt_tokens' => 1900, 'completion_tokens' => 600]
        );

        // 5. Deduct premium credit for complex reasoning agent
        $walletService->deductCredits(
            $this->demoUser,
            4.0,
            'premium',
            'Deep Reasoning Agent - Complex Workflow',
            'req_ai_prem_882',
            ['model' => 'o3-mini', 'reasoning_tokens' => 3200]
        );
    }

    /**
     * Get sample product and price for checkout demos
     */
    protected function getSampleProductAndPrice(): void
    {
        $product = \App\Models\Product::where('is_active', true)
            ->whereHas('prices', function ($query) {
                $query->where('is_active', true);
            })
            ->with('prices')
            ->first();

        if ($product) {
            $this->productId = $product->id;
            $this->priceId = $product->prices->first()?->id;
        } else {
            $this->productId = null;
            $this->priceId = null;
        }
    }
}
