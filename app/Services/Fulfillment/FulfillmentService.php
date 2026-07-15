<?php

namespace App\Services\Fulfillment;

use App\Models\Fulfillment;
use App\Models\Order;
use App\Models\Subscription;
use App\Notifications\DigitalOrderFulfilledNotification;
use App\Notifications\OrderFulfilledNotification;
use App\Notifications\PhysicalOrderFulfilledNotification;
use App\Notifications\ServiceOrderFulfilledNotification;
use App\Notifications\SubscriptionCreatedNotification;
use App\Services\Settings\SettingsManager;
use Illuminate\Support\Facades\Log;

class FulfillmentService
{
    public function __construct(
        protected SettingsManager $settings
    ) {}

    /**
     * Process fulfillment for a completed purchase
     */
    public function processPurchaseFulfillment(Order $order)
    {
        try {
            // Get product type to determine fulfillment method
            $productType = $order->productPrice->product->type;

            // Create fulfillment record
            $fulfillment = Fulfillment::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'type' => $productType,
            ]);

            // Process different types of products
            $result = match ($productType) {
                'digital' => $this->fulfillDigitalProduct($order, $fulfillment),
                'physical' => $this->fulfillPhysicalProduct($order, $fulfillment),
                'service' => $this->fulfillServiceProduct($order, $fulfillment),
                'subscription' => $this->fulfillSubscriptionProduct($order, $fulfillment),
                default => false
            };

            if ($result) {
                // Mark fulfillment as completed
                $fulfillment->update([
                    'status' => 'completed',
                    'fulfilled_at' => now(),
                ]);

                // Notify customer with product-type-specific notification
                // Subscriptions don't get fulfillment notifications - they get subscription notifications separately
                if ($productType !== 'subscription') {
                    $notification = match ($productType) {
                        'digital' => new DigitalOrderFulfilledNotification($order, $fulfillment),
                        'physical' => new PhysicalOrderFulfilledNotification($order, $fulfillment),
                        'service' => new ServiceOrderFulfilledNotification($order, $fulfillment),
                        default => new OrderFulfilledNotification($order, $fulfillment)
                    };

                    $order->user->notify($notification);
                }
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Error processing fulfillment: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Process fulfillment for subscription renewal
     */
    public function processSubscriptionRenewalFulfillment(Subscription $subscription)
    {
        try {
            // For subscription renewals, we typically just extend access
            // Create a record in fulfillments table for tracking
            $fulfillment = Fulfillment::create([
                'order_id' => $subscription->order_id, // Use the original order
                'status' => 'completed',
                'type' => 'subscription_access',
                'meta' => [
                    'subscription_id' => $subscription->id,
                    'renewal_date' => now()->toDateTimeString(),
                    'next_renewal' => $subscription->next_billing_date,
                ],
                'fulfilled_at' => now(),
            ]);

            // Here you would extend access if needed
            // In case of a content subscription, you might need to refresh access tokens, etc.

            return true;
        } catch (\Exception $e) {
            Log::error('Error processing subscription renewal: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Fulfill a digital product (e.g., generate download links, license keys)
     */
    private function fulfillDigitalProduct(Order $order, Fulfillment $fulfillment)
    {
        // Example implementation for digital product fulfillment
        try {
            $meta = [];

            // Check if license generator is enabled
            if ($this->settings->get('fulfillment_providers.license_generator_enabled', true)) {
                $licenseKey = $this->generateLicenseKey();
                $meta['license_key'] = $licenseKey;
            }

            // Check if downloader is enabled
            if ($this->settings->get('fulfillment_providers.downloader_enabled', true)) {
                $downloadLink = $this->generateDownloadLink($order->productPrice->product);
                $expiryHours = $this->settings->get('fulfillment_providers.downloader_link_expiry', 48);
                $maxDownloads = $this->settings->get('fulfillment_providers.downloader_max_downloads', 5);

                // Write both keys to satisfy API docs and existing consumers
                $meta['download_url'] = $downloadLink;
                $meta['download_link'] = $downloadLink;
                $meta['expires_at'] = now()->addHours($expiryHours)->toDateTimeString();
                $meta['max_downloads'] = $maxDownloads;
                $meta['download_count'] = 0;
            }

            // Store fulfillment details
            $fulfillment->update(['meta' => $meta]);

            // You might want to send the license key and download link via email
            // This is often handled by the notification, but you can also do it here

            return true;
        } catch (\Exception $e) {
            Log::error('Error fulfilling digital product: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Fulfill a physical product (e.g., prepare for shipping)
     */
    private function fulfillPhysicalProduct(Order $order, Fulfillment $fulfillment)
    {
        // Example implementation for physical product fulfillment
        try {
            // In a real application, you might integrate with a shipping API
            // or create a task in your fulfillment system

            // For example, create a shipping label
            $trackingNumber = 'TRACK-'.rand(10000, 99999);
            $provider = 'Example Shipping Co.';

            $fulfillment->update([
                'status' => 'processing',
                'tracking_id' => $trackingNumber,
                'provider' => $provider,
                'meta' => [
                    'shipping_address' => [
                        'name' => $order->billing_name,
                        'address' => $order->billing_address,
                        'city' => $order->billing_city,
                        'state' => $order->billing_state,
                        'zipcode' => $order->billing_zipcode,
                        'country' => $order->billing_country,
                    ],
                    'estimated_delivery' => now()->addDays(5)->toDateTimeString(),
                    'tracking_number' => $trackingNumber,
                    'carrier' => $provider,
                ],
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error fulfilling physical product: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Fulfill a service product (e.g., schedule an appointment)
     */
    private function fulfillServiceProduct(Order $order, Fulfillment $fulfillment)
    {
        // Example implementation for service product fulfillment
        try {
            // For a service, you might schedule an appointment or create an account
            // in your service delivery system

            // For example, generate a service activation code
            $serviceCode = 'SERVICE-'.strtoupper(substr(md5(uniqid()), 0, 8));

            // Update fulfillment record
            $fulfillment->update([
                'meta' => [
                    'service_code' => $serviceCode,
                    'instructions' => 'Contact our team to schedule your appointment.',
                    'valid_until' => now()->addMonths(3)->toDateTimeString(),
                ],
            ]);

            // You might also want to create a task for your service team
            // or send an internal notification

            return true;
        } catch (\Exception $e) {
            Log::error('Error fulfilling service product: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Fulfill a subscription product (e.g., activate subscription access)
     */
    private function fulfillSubscriptionProduct(Order $order, Fulfillment $fulfillment)
    {
        // Example implementation for subscription initial fulfillment
        try {
            // Get or create subscription based on the order
            $subscription = Subscription::where('order_id', $order->id)->first();

            if (! $subscription) {
                Log::error('Subscription not found for order: '.$order->id);

                return false;
            }

            // Activate the subscription if it's not already active
            $wasActivated = false;
            if ($subscription->status !== 'active' && $subscription->status !== 'trialing') {
                $subscription->update([
                    'status' => $subscription->trial_ends_at && $subscription->trial_ends_at->isFuture()
                        ? 'trialing'
                        : 'active',
                ]);
                $wasActivated = true;
            }

            // Update fulfillment record
            $fulfillment->update([
                'type' => 'subscription_access',
                'meta' => [
                    'subscription_id' => $subscription->id,
                    'activated_at' => now()->toDateTimeString(),
                    'next_billing_date' => $subscription->next_billing_date,
                    'trial_ends_at' => $subscription->trial_ends_at,
                ],
            ]);

            // Send subscription created notification
            if ($wasActivated) {
                $order->user->notify(new SubscriptionCreatedNotification($subscription));
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error fulfilling subscription product: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Generate a license key for digital products
     */
    private function generateLicenseKey()
    {
        $keyLength = $this->settings->get('fulfillment_providers.license_generator_key_length', 16);
        $prefix = $this->settings->get('fulfillment_providers.license_generator_prefix', '');

        // Generate random key
        $key = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, $keyLength));

        // Add prefix if configured
        return $prefix ? $prefix.$key : $key;
    }

    /**
     * Generate a download link for a digital product
     */
    private function generateDownloadLink($product)
    {
        // Simple download link generator for example purposes
        $token = bin2hex(random_bytes(32));

        return url('/download/'.$product->id.'/'.$token);
    }
}
