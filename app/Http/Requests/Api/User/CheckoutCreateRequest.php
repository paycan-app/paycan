<?php

namespace App\Http\Requests\Api\User;

use App\Models\ProductPrice;
use App\Rules\PriceBelongsToProduct;
use App\Rules\ValidPaymentGateway;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CheckoutCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'product_price_id' => [
                'required',
                'exists:product_prices,id',
                new PriceBelongsToProduct($this->input('product_id')),
            ],
            'billing_email' => 'nullable|email',
            'billing_name' => 'nullable|string|max:255',
            'billing_country' => 'nullable|string|max:3',
            'billing_state' => 'nullable|string|max:3',
            'quantity' => 'nullable|integer|min:1',
            'shipping_address' => 'nullable|array',
            'gateway' => ['required', 'string', new ValidPaymentGateway],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $productPriceId = $this->input('product_price_id');

            if ($productPriceId) {
                $price = ProductPrice::with('product')->find($productPriceId);

                if ($price) {
                    // Validate product is active
                    if (! $price->is_active || ! $price->product->is_active) {
                        $validator->errors()->add('product_price_id', 'Product is not available');
                    }

                    // Validate gateway against the product price (generic gateway
                    // validity is covered by the ValidPaymentGateway rule in rules())
                    $gateway = $this->input('gateway');
                    if ($gateway && ! $validator->errors()->has('gateway')) {
                        if (! \App\Services\Payment\PaymentGatewayRegistry::canUseForProductPrice($gateway, $price)) {
                            $validator->errors()->add('gateway', 'The selected payment gateway is not supported for this product price.');
                        }
                    }
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Product ID is required.',
            'product_id.exists' => 'The selected product does not exist.',
            'product_price_id.required' => 'Product price ID is required.',
            'product_price_id.exists' => 'The selected product price does not exist.',
            'billing_email.email' => 'Billing email must be a valid email address.',
            'billing_name.max' => 'Billing name must not exceed 255 characters.',
            'billing_country.max' => 'Billing country must not exceed 3 characters.',
            'billing_state.max' => 'Billing state must not exceed 3 characters.',
            'quantity.integer' => 'Quantity must be an integer.',
            'quantity.min' => 'Quantity must be at least 1.',
            'shipping_address.array' => 'Shipping address must be an array.',
            'gateway.required' => 'Payment gateway is required.',
        ];
    }
}
