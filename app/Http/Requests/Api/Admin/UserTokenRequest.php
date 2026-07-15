<?php

namespace App\Http\Requests\Api\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UserTokenRequest extends FormRequest
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
        $userId = $this->input('user_id');
        $userExists = $userId ? User::where('id', $userId)->exists() : false;

        $rules = [
            'user_id' => 'required|string|max:100',
        ];

        if (! $userExists) {
            // New user - name and email are required
            $rules['user'] = 'required|array';
            $rules['user.name'] = 'required|string|max:255';
            $rules['user.email'] = 'required|email|max:255|unique:users,email';
        } else {
            // Existing user - name and email are optional
            $rules['user'] = 'nullable|array';
            $rules['user.name'] = 'nullable|string|max:255';
            $rules['user.email'] = 'nullable|email|max:255';
        }

        return $rules;
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $userId = $this->input('user_id');
            $userData = $this->input('user');
            $user = User::find($userId);

            // If updating existing user, validate unique email (excluding current user)
            if ($user && $userData && isset($userData['email']) && $userData['email']) {
                $emailExists = User::where('email', $userData['email'])
                    ->where('id', '!=', $user->id)
                    ->exists();

                if ($emailExists) {
                    $validator->errors()->add('user.email', 'The email has already been taken.');
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
            'user_id.required' => 'User ID is required.',
            'user_id.string' => 'User ID must be a string.',
            'user_id.max' => 'User ID must not exceed 100 characters.',
            'user.required' => 'User data is required for new users.',
            'user.array' => 'User data must be an array.',
            'user.name.required' => 'User name is required for new users.',
            'user.name.string' => 'User name must be a string.',
            'user.name.max' => 'User name must not exceed 255 characters.',
            'user.email.required' => 'User email is required for new users.',
            'user.email.email' => 'User email must be a valid email address.',
            'user.email.max' => 'User email must not exceed 255 characters.',
            'user.email.unique' => 'The email has already been taken.',
        ];
    }
}
