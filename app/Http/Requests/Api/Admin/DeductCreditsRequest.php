<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DeductCreditsRequest extends FormRequest
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
            'user_id' => 'required|string|exists:users,id',
            'wallet_type' => 'nullable|string|max:50',
            'amount' => 'required|numeric|min:0.0001',
            'reference_id' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:255',
            'meta' => 'nullable|array',
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
            'user_id.required' => 'User ID is required',
            'user_id.exists' => 'User not found with the provided ID',
            'amount.required' => 'Deduction amount is required',
            'amount.numeric' => 'Deduction amount must be a number',
            'amount.min' => 'Deduction amount must be at least 0.0001',
        ];
    }
}
