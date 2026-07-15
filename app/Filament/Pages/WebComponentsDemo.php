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
