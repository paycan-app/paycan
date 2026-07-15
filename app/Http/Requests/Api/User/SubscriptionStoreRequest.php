<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionStoreRequest extends FormRequest
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
            'product_price_id' => 'required|exists:product_prices,id',
            'gateway' => 'required|in:stripe,paypal',
            'payment_method_id' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'product_price_id.required' => 'Product price ID is required.',
            'product_price_id.exists' => 'The selected product price does not exist.',
            'gateway.required' => 'Payment gateway is required.',
            'gateway.in' => 'The payment gateway must be either stripe or paypal.',
        ];
    }
}
