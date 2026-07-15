<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
        $product = $this->route('product');

        return [
            'title' => 'string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,'.$product->id,
            'description' => 'nullable|string',
            'type' => 'in:digital,physical,service,subscription',
            'is_active' => 'boolean',
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
            'title.string' => 'Product title must be a string.',
            'title.max' => 'Product title must not exceed 255 characters.',
            'slug.string' => 'Product slug must be a string.',
            'slug.max' => 'Product slug must not exceed 255 characters.',
            'slug.unique' => 'This product slug has already been taken.',
            'description.string' => 'Product description must be a string.',
            'type.in' => 'Product type must be one of: digital, physical, service, subscription.',
            'is_active.boolean' => 'is_active must be a boolean value.',
            'meta.array' => 'Meta must be an array.',
        ];
    }
}
