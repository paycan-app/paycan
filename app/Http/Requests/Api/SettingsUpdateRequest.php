<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SettingsUpdateRequest extends FormRequest
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
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable',
            'settings.*.type' => 'nullable|string|in:string,integer,boolean,array,encrypted',
            'settings.*.is_public' => 'nullable|boolean',
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
            'settings.required' => 'Settings array is required.',
            'settings.array' => 'Settings must be an array.',
            'settings.*.key.required' => 'Each setting must have a key.',
            'settings.*.key.string' => 'Setting keys must be strings.',
            'settings.*.type.in' => 'Setting type must be one of: string, integer, boolean, array, encrypted.',
            'settings.*.is_public.boolean' => 'is_public must be a boolean value.',
        ];
    }
}
