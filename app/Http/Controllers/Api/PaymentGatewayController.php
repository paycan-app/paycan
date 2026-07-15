<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Services\Payment\PaymentGatewayRegistry;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentGatewayController extends Controller
{
    use ApiResponse;

    /**
     * Get all available payment gateways
     */
    public function index(): JsonResponse
    {
        $gateways = PaymentGatewayRegistry::enabled();

        $formatted = [];
        foreach ($gateways as $key => $config) {
            $formatted[] = [
                'id' => $key,
                'name' => $config['name'],
                'icon' => $config['icon'],
                'description' => $config['description'],
                'supports_subscriptions' => $config['supports_subscriptions'],
                'supported_currencies' => $config['supported_currencies'],
                'supported_product_types' => $config['supported_product_types'],
            ];
        }

        return $this->success($formatted);
    }

    /**
     * Get available payment gateways for a specific product
     */
    public function forProduct(Request $request, string $productId): JsonResponse
    {
        $product = Product::findOrFail($productId);
        $gateways = PaymentGatewayRegistry::forProduct($product);

        $formatted = [];
        foreach ($gateways as $key => $config) {
            $formatted[] = [
                'id' => $key,
                'name' => $config['name'],
                'icon' => $config['icon'],
                'description' => $config['description'],
                'supports_subscriptions' => $config['supports_subscriptions'],
            ];
        }

        return $this->success($formatted);
    }

    /**
     * Get available payment gateways for a specific product price
     */
    public function forProductPrice(Request $request, string $productPriceId): JsonResponse
    {
        $productPrice = ProductPrice::with('product')->findOrFail($productPriceId);
        $gateways = PaymentGatewayRegistry::forProductPrice($productPrice);

        $formatted = [];
        foreach ($gateways as $key => $config) {
            $formatted[] = [
                'id' => $key,
                'name' => $config['name'],
                'icon' => $config['icon'],
                'description' => $config['description'],
                'supports_subscriptions' => $config['supports_subscriptions'],
            ];
        }

        return $this->success($formatted);
    }

    /**
     * Validate if a gateway can be used for checkout
     */
    public function validate(Request $request): JsonResponse
    {
        $request->validate([
            'gateway' => 'required|string',
            'product_id' => 'sometimes|string|exists:products,id',
            'product_price_id' => 'sometimes|string|exists:product_prices,id',
        ]);

        $gateway = $request->input('gateway');

        // Check if gateway exists
        if (! PaymentGatewayRegistry::exists($gateway)) {
            return $this->error('Invalid payment gateway', 422, [
                'gateway' => ['The selected payment gateway is invalid.'],
            ]);
        }

        // Check if gateway is enabled
        $enabledGateways = PaymentGatewayRegistry::enabled();
        if (! isset($enabledGateways[$gateway])) {
            return $this->error('Payment gateway is not enabled', 422, [
                'gateway' => ['The selected payment gateway is not enabled.'],
            ]);
        }

        // Validate against product if provided
        if ($request->has('product_id')) {
            $product = Product::findOrFail($request->input('product_id'));

            if (! PaymentGatewayRegistry::canUseForProduct($gateway, $product)) {
                return $this->error('Payment gateway not supported for this product', 422, [
                    'gateway' => ['The selected payment gateway is not supported for this product.'],
                ]);
            }
        }

        // Validate against product price if provided
        if ($request->has('product_price_id')) {
            $productPrice = ProductPrice::with('product')->findOrFail($request->input('product_price_id'));

            if (! PaymentGatewayRegistry::canUseForProductPrice($gateway, $productPrice)) {
                return $this->error('Payment gateway not supported for this product price', 422, [
                    'gateway' => ['The selected payment gateway is not supported for this product price.'],
                ]);
            }
        }

        return $this->success([
            'valid' => true,
            'gateway' => $enabledGateways[$gateway]['name'],
        ]);
    }
}
