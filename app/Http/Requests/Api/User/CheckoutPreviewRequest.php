<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutPreviewRequest extends FormRequest
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
            'product_id' => 'sometimes|exists:products,id',
            'product_price_id' => 'sometimes|exists:product_prices,id',
            'selected_price_id' => 'sometimes|exists:product_prices,id',
            'quantity' => 'sometimes|integer|min:1',
            'billing_country' => 'sometimes|string|max:3',
            'billing_state' => 'sometimes|string|max:3',
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
            'product_id.exists' => 'The selected product does not exist.',
            'product_price_id.exists' => 'The selected product price does not exist.',
            'selected_price_id.exists' => 'The selected price does not exist.',
            'quantity.integer' => 'Quantity must be an integer.',
            'quantity.min' => 'Quantity must be at least 1.',
            'billing_country.max' => 'Billing country must not exceed 3 characters.',
            'billing_state.max' => 'Billing state must not exceed 3 characters.',
        ];
    }
}
