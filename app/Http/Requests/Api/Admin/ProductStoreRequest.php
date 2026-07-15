<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'type' => 'required|in:digital,physical,service,subscription',
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
            'title.required' => 'Product title is required.',
            'title.string' => 'Product title must be a string.',
            'title.max' => 'Product title must not exceed 255 characters.',
            'slug.string' => 'Product slug must be a string.',
            'slug.max' => 'Product slug must not exceed 255 characters.',
            'slug.unique' => 'This product slug has already been taken.',
            'description.string' => 'Product description must be a string.',
            'type.required' => 'Product type is required.',
            'type.in' => 'Product type must be one of: digital, physical, service, subscription.',
            'is_active.boolean' => 'is_active must be a boolean value.',
            'meta.array' => 'Meta must be an array.',
        ];
    }
}
