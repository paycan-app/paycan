<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Digital Products
        $digitalProducts = [
            [
                'title' => 'Premium Software License',
                'description' => 'Advanced productivity software with lifetime license and premium support.',
                'type' => 'digital',
                'is_active' => true,
                'meta' => [
                    'category' => 'software',
                    'version' => '2.1.0',
                    'platforms' => ['Windows', 'macOS', 'Linux'],
                    'license_type' => 'single_user',
                    'download_size' => '250MB',
                    'features' => [
                        'Unlimited projects',
                        'Premium templates',
                        '24/7 support',
                        'Cloud sync',
                    ],
                ],
                'prices' => [
                    [
                        'title' => 'Standard License',
                        // 'description' => 'Single user license with 1 year of updates',
                        'amount' => 199.99,
                        'billing_period' => 'once',
                        'is_active' => true,
                        'gateway_data' => [
                            'stripe' => ['price_id' => 'price_digital_standard'],
                            'paypal' => ['plan_id' => 'digital_standard_plan'],
                        ],
                    ],
                    [
                        'title' => 'Team License',
                        // 'description' => '5 user license with priority support',
                        'amount' => 499.99,
                        'billing_period' => 'once',
                        'is_active' => true,
                        'gateway_data' => [
                            'stripe' => ['price_id' => 'price_digital_team'],
                            'paypal' => ['plan_id' => 'digital_team_plan'],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Complete Laravel Course',
                'description' => 'Master Laravel from beginner to advanced with real-world projects.',
                'type' => 'digital',
                'is_active' => true,
                'meta' => [
                    'category' => 'education',
                    'content_type' => 'course',
                    'duration' => '40+ hours',
                    'skill_level' => 'beginner_to_advanced',
                    'includes' => [
                        '12 modules',
                        '150+ lessons',
                        'Source code',
                        'Certificate',
                    ],
                ],
                'prices' => [
                    [
                        'title' => 'Full Course Access',
                        // 'description' => 'Lifetime access to all course materials',
                        'amount' => 149.99,
                        'billing_period' => 'once',
                        'is_active' => true,
                        'gateway_data' => [
                            'stripe' => ['price_id' => 'price_course_full'],
                            'paypal' => ['plan_id' => 'course_full_plan'],
                        ],
                    ],
                ],
            ],
        ];

        // Physical Products
        $physicalProducts = [
            [
                'title' => 'Wireless Noise-Canceling Headphones',
                'description' => 'Premium wireless headphones with active noise cancellation and 30-hour battery.',
                'type' => 'physical',
                'is_active' => true,
                'meta' => [
                    'category' => 'electronics',
                    'brand' => 'TechSound',
                    'weight' => '0.3kg',
                    'dimensions' => '20x18x8 cm',
                    'color_options' => ['Black', 'White', 'Blue'],
                    'features' => [
                        'Active noise cancellation',
                        '30-hour battery life',
                        'Wireless charging case',
                        'Hi-Fi sound quality',
                    ],
                ],
                'prices' => [
                    [
                        'title' => 'Standard Price',
                        // 'description' => 'Free shipping included',
                        'amount' => 299.99,
                        'billing_period' => 'once',
                        'is_active' => true,
                        'gateway_data' => [
                            'stripe' => ['price_id' => 'price_headphones'],
                            'paypal' => ['plan_id' => 'headphones_plan'],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Smart Fitness Tracker',
                'description' => 'Track your health and fitness goals with advanced sensors and 7-day battery life.',
                'type' => 'physical',
                'is_active' => true,
                'meta' => [
                    'category' => 'wearables',
                    'brand' => 'FitTech',
                    'weight' => '0.05kg',
                    'water_resistant' => true,
                    'battery_life' => '7 days',
                    'features' => [
                        'Heart rate monitoring',
                        'Sleep tracking',
                        '50+ workout modes',
                        'Smartphone notifications',
                    ],
                ],
                'prices' => [
                    [
                        'title' => 'Standard Edition',
                        // 'description' => 'Includes charging cable and user manual',
                        'amount' => 199.99,
                        'billing_period' => 'once',
                        'is_active' => true,
                        'gateway_data' => [
                            'stripe' => ['price_id' => 'price_fitness_tracker'],
                            'paypal' => ['plan_id' => 'fitness_tracker_plan'],
                        ],
                    ],
                ],
            ],
        ];

        // Service Products
        $serviceProducts = [
            [
                'title' => 'Website Design Consultation',
                'description' => '1-on-1 consultation with our expert designers to create your perfect website.',
                'type' => 'service',
                'is_active' => true,
                'meta' => [
                    'category' => 'consultation',
                    'duration' => '2 hours',
                    'delivery_method' => 'video_call',
                    'expertise' => ['web_design', 'UX/UI', 'branding'],
                    'includes' => [
                        'Design strategy session',
                        'Wireframe review',
                        'Brand consultation',
                        'Follow-up report',
                    ],
                ],
                'prices' => [
                    [
                        'title' => 'Single Session',
                        // 'description' => '2-hour design consultation',
                        'amount' => 299.99,
                        'billing_period' => 'once',
                        'is_active' => true,
                        'gateway_data' => [
                            'stripe' => ['price_id' => 'price_design_consultation'],
                            'paypal' => ['plan_id' => 'design_consultation_plan'],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Monthly Business Consulting',
                'description' => 'Ongoing business strategy and growth consulting with weekly check-ins.',
                'type' => 'service',
                'is_active' => true,
                'meta' => [
                    'category' => 'consulting',
                    'type' => 'recurring_consultation',
                    'hours_per_month' => 10,
                    'meeting_frequency' => 'weekly',
                    'includes' => [
                        'Weekly strategy calls',
                        'Growth planning',
                        'Market analysis',
                        'Monthly reports',
                    ],
                ],
                'prices' => [
                    [
                        'title' => 'Monthly Retainer',
                        // 'description' => '10 hours of consulting per month',
                        'amount' => 1500.00,
                        'billing_period' => 'monthly',
                        'is_active' => true,
                        'gateway_data' => [
                            'stripe' => ['price_id' => 'price_business_consulting'],
                            'paypal' => ['plan_id' => 'business_consulting_plan'],
                        ],
                    ],
                ],
            ],
        ];

        // Subscription Products
        $subscriptionProducts = [
            [
                'title' => 'Premium Cloud Storage',
                'description' => 'Secure cloud storage with advanced features and collaboration tools.',
                'type' => 'subscription',
                'is_active' => true,
                'meta' => [
                    'category' => 'cloud_storage',
                    'storage_limit' => '1TB',
                    'max_users' => 5,
                    'features' => [
                        '1TB secure storage',
                        'File sharing & collaboration',
                        'Automatic backup',
                        'Version history',
                        'Priority support',
                    ],
                ],
                'prices' => [
                    [
                        'title' => 'Monthly Plan',
                        // 'description' => 'Billed monthly, cancel anytime',
                        'amount' => 19.99,
                        'billing_period' => 'monthly',
                        'is_active' => true,
                        'gateway_data' => [
                            'stripe' => ['price_id' => 'price_cloud_monthly'],
                            'paypal' => ['plan_id' => 'cloud_monthly_plan'],
                        ],
                    ],
                    [
                        'title' => 'Annual Plan',
                        // 'description' => 'Save 20% with annual billing',
                        'amount' => 199.99,
                        'billing_period' => 'yearly',
                        'is_active' => true,
                        'gateway_data' => [
                            'stripe' => ['price_id' => 'price_cloud_yearly'],
                            'paypal' => ['plan_id' => 'cloud_yearly_plan'],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Pro Developer Tools',
                'description' => 'Complete suite of development tools and APIs for professional developers.',
                'type' => 'subscription',
                'is_active' => true,
                'meta' => [
                    'category' => 'developer_tools',
                    'api_calls_limit' => 100000,
                    'max_projects' => 'unlimited',
                    'features' => [
                        '100k API calls/month',
                        'Unlimited projects',
                        'Advanced analytics',
                        'Priority support',
                        'Custom integrations',
                    ],
                ],
                'prices' => [
                    [
                        'title' => 'Monthly Pro',
                        // 'description' => 'Full access to all developer tools',
                        'amount' => 49.99,
                        'billing_period' => 'monthly',
                        'is_active' => true,
                        'gateway_data' => [
                            'stripe' => ['price_id' => 'price_dev_tools_monthly'],
                            'paypal' => ['plan_id' => 'dev_tools_monthly_plan'],
                        ],
                    ],
                    [
                        'title' => 'Annual Pro',
                        // 'description' => 'Save 25% with annual billing',
                        'amount' => 449.99,
                        'billing_period' => 'yearly',
                        'is_active' => true,
                        'gateway_data' => [
                            'stripe' => ['price_id' => 'price_dev_tools_yearly'],
                            'paypal' => ['plan_id' => 'dev_tools_yearly_plan'],
                        ],
                    ],
                ],
            ],
        ];

        // Create all products and their prices
        $allProducts = array_merge($digitalProducts, $physicalProducts, $serviceProducts, $subscriptionProducts);

        foreach ($allProducts as $productData) {
            $prices = $productData['prices'];
            unset($productData['prices']);

            // Generate slug from title
            $productData['slug'] = strtolower(str_replace([' ', '/', '&', '-'], ['-', '-', 'and', '-'], $productData['title']));
            $productData['slug'] = preg_replace('/[^a-z0-9\-]/', '', $productData['slug']);
            $productData['slug'] = preg_replace('/-+/', '-', $productData['slug']);
            $productData['slug'] = trim($productData['slug'], '-');

            // Add ULID for the product
            $productData['id'] = Str::ulid()->toString();

            $product = Product::create($productData);

            foreach ($prices as $priceData) {
                $priceData['product_id'] = $product->id;

                // Generate slug for price from title
                $priceData['slug'] = strtolower(str_replace([' ', '/', '&', '-'], ['-', '-', 'and', '-'], $priceData['title']));
                $priceData['slug'] = preg_replace('/[^a-z0-9\-]/', '', $priceData['slug']);
                $priceData['slug'] = preg_replace('/-+/', '-', $priceData['slug']);
                $priceData['slug'] = trim($priceData['slug'], '-');
                $priceData['slug'] = $product->slug.'-'.$priceData['slug'];

                // Add ULID for the product price
                $priceData['id'] = Str::ulid()->toString();

                ProductPrice::create($priceData);
            }
        }
    }
}
