<?php

namespace App\Rules;

use App\Models\Product;
use App\Models\ProductPrice;
use App\Services\Payment\PaymentGatewayRegistry;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidPaymentGateway implements ValidationRule
{
    protected ?Product $product = null;

    protected ?ProductPrice $productPrice = null;

    public function __construct(?Product $product = null, ?ProductPrice $productPrice = null)
    {
        $this->product = $product;
        $this->productPrice = $productPrice;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if gateway exists
        if (! PaymentGatewayRegistry::exists($value)) {
            $fail('The selected payment gateway is invalid.');

            return;
        }

        // Check if gateway is enabled
        $enabledGateways = PaymentGatewayRegistry::enabled();
        if (! isset($enabledGateways[$value])) {
            $fail('The selected payment gateway is not enabled.');

            return;
        }

        // Validate against product price if provided
        if ($this->productPrice) {
            if (! PaymentGatewayRegistry::canUseForProductPrice($value, $this->productPrice)) {
                $fail('The selected payment gateway is not supported for this product price.');

                return;
            }
        }
        // Validate against product if provided
        elseif ($this->product) {
            if (! PaymentGatewayRegistry::canUseForProduct($value, $this->product)) {
                $fail('The selected payment gateway is not supported for this product.');

                return;
            }
        }
    }

    /**
     * Create a rule instance for a specific product
     */
    public static function forProduct(Product $product): self
    {
        return new self($product);
    }

    /**
     * Create a rule instance for a specific product price
     */
    public static function forProductPrice(ProductPrice $productPrice): self
    {
        return new self(null, $productPrice);
    }
}
