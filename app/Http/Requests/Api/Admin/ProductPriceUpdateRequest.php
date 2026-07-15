<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductPriceUpdateRequest extends FormRequest
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
        $price = $this->route('price');

        return [
            'title' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:product_prices,slug,'.$price->id,
            'amount' => 'numeric|min:0',
            'currency' => 'string|size:3',
            'billing_period' => 'in:once,daily,weekly,monthly,yearly',
            'trial_days' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
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
            'title.string' => 'Price title must be a string.',
            'title.max' => 'Price title must not exceed 255 characters.',
            'slug.string' => 'Price slug must be a string.',
            'slug.max' => 'Price slug must not exceed 255 characters.',
            'slug.unique' => 'This price slug has already been taken.',
            'amount.numeric' => 'Price amount must be a number.',
            'amount.min' => 'Price amount must be at least 0.',
            'currency.string' => 'Currency must be a string.',
            'currency.size' => 'Currency must be exactly 3 characters.',
            'billing_period.in' => 'Billing period must be one of: once, daily, weekly, monthly, yearly.',
            'trial_days.integer' => 'Trial days must be an integer.',
            'trial_days.min' => 'Trial days must be at least 0.',
            'is_active.boolean' => 'is_active must be a boolean value.',
        ];
    }
}
