<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Database\Seeder;

class PortalDemoSeeder extends Seeder
{
    /**
     * Seed demo products for portal testing
     */
    public function run(): void
    {
        // Digital Product - E-book
        $ebook = Product::firstOrCreate(
            ['id' => 'demo-ebook'],
            [
                'title' => 'Complete Laravel Guide',
                'slug' => 'complete-laravel-guide',
                'description' => 'Master Laravel development with this comprehensive guide. Includes code examples, best practices, and real-world projects.',
                'type' => 'digital',
                'is_active' => true,
            ]
        );

        ProductPrice::firstOrCreate(
            ['id' => 'demo-ebook-price'],
            [
                'product_id' => $ebook->id,
                'title' => 'Standard Edition',
                'slug' => 'standard-edition',
                'amount' => 49.99,
                'currency' => 'USD',
                'billing_period' => 'once',
                'is_active' => true,
            ]
        );

        // Service Product
        $consulting = Product::firstOrCreate(
            ['id' => 'demo-consulting'],
            [
                'title' => 'Web Development Consultation',
                'slug' => 'web-dev-consultation',
                'description' => 'One-on-one consultation with expert developers. Get personalized advice for your project.',
                'type' => 'service',
                'is_active' => true,
            ]
        );

        ProductPrice::firstOrCreate(
            ['id' => 'demo-consulting-hourly'],
            [
                'product_id' => $consulting->id,
                'title' => 'Hourly Rate',
                'slug' => 'hourly-rate',
                'amount' => 150.00,
                'currency' => 'USD',
                'billing_period' => 'once',
                'is_active' => true,
            ]
        );

        // Subscription Product - SaaS
        $saas = Product::firstOrCreate(
            ['id' => 'demo-saas'],
            [
                'title' => 'Premium SaaS Plan',
                'slug' => 'premium-saas-plan',
                'description' => 'Access to all premium features including advanced analytics, priority support, and unlimited projects.',
                'type' => 'subscription',
                'is_active' => true,
            ]
        );

        ProductPrice::firstOrCreate(
            ['id' => 'demo-saas-monthly'],
            [
                'product_id' => $saas->id,
                'title' => 'Monthly',
                'slug' => 'monthly',
                'amount' => 29.99,
                'currency' => 'USD',
                'billing_period' => 'monthly',
                'is_active' => true,
            ]
        );

        ProductPrice::firstOrCreate(
            ['id' => 'demo-saas-yearly'],
            [
                'product_id' => $saas->id,
                'title' => 'Yearly (Save 20%)',
                'slug' => 'yearly',
                'amount' => 287.88,
                'currency' => 'USD',
                'billing_period' => 'yearly',
                'is_active' => true,
            ]
        );

        // Physical Product
        $tshirt = Product::firstOrCreate(
            ['id' => 'demo-tshirt'],
            [
                'title' => 'Laravel Developer T-Shirt',
                'slug' => 'laravel-tshirt',
                'description' => 'Premium quality t-shirt for Laravel developers. 100% cotton, available in multiple sizes.',
                'type' => 'physical',
                'is_active' => true,
            ]
        );

        ProductPrice::firstOrCreate(
            ['id' => 'demo-tshirt-price'],
            [
                'product_id' => $tshirt->id,
                'title' => 'Standard Size',
                'slug' => 'standard-size',
                'amount' => 24.99,
                'currency' => 'USD',
                'billing_period' => 'once',
                'is_active' => true,
            ]
        );
    }
}
