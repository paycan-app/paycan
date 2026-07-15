<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\View\View;

class AccountModalsDemoController extends Controller
{
    /**
     * Display the account modals demo page
     *
     * This page demonstrates the Subscriptions, Orders, and Transactions modals
     */
    public function index(): View
    {
        // Get or create a demo user with data
        $user = User::where('email', 'demo@paycan.test')->first();

        if (! $user) {
            $user = User::factory()->create([
                'name' => 'Demo User',
                'email' => 'demo@paycan.test',
            ]);
        }

        // Create demo data if user has no orders
        if ($user->orders()->count() === 0) {
            $this->seedDemoData($user);
        }

        // Generate a demo user token for API authentication
        // In production, this would come from your authentication flow
        $token = $user->createToken('demo-token')->plainTextToken;

        return view('account-modals-demo', [
            'token' => $token,
            'user' => $user,
        ]);
    }

    /**
     * Seed demo data for the user
     */
    private function seedDemoData(User $user): void
    {
        // Get or create demo products
        $products = Product::active()->take(3)->get();

        if ($products->isEmpty()) {
            // If no products exist, just return - user should create them in admin
            return;
        }

        // Create a few completed orders
        foreach ($products->take(2) as $product) {
            $price = $product->prices()->active()->first();
            if (! $price) {
                continue;
            }

            $order = Order::factory()->create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'product_price_id' => $price->id,
                'status' => 'completed',
                'gateway' => fake()->randomElement(['stripe', 'paypal']),
                'total' => $price->amount,
                'currency' => $price->currency,
            ]);

            // Create a transaction for the order
            Transaction::factory()->create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'status' => 'succeeded',
                'amount' => $price->amount,
                'currency' => $price->currency,
                'gateway' => $order->gateway,
                'type' => 'payment',
            ]);
        }

        // Create an active subscription if a recurring price exists
        $recurringPrice = Product::active()
            ->whereHas('prices', function ($query) {
                $query->where('billing_period', '!=', 'once')->active();
            })
            ->with(['prices' => function ($query) {
                $query->where('billing_period', '!=', 'once')->active();
            }])
            ->first()?->prices->first();

        if ($recurringPrice) {
            $subscription = Subscription::factory()->create([
                'user_id' => $user->id,
                'product_id' => $recurringPrice->product_id,
                'product_price_id' => $recurringPrice->id,
                'status' => 'active',
                'gateway' => 'stripe',
                'next_billing_date' => now()->addMonth(),
            ]);

            // Create a transaction for the subscription
            Transaction::factory()->create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'status' => 'succeeded',
                'amount' => $recurringPrice->amount,
                'currency' => $recurringPrice->currency,
                'gateway' => 'stripe',
                'type' => 'subscription',
            ]);
        }
    }
}
