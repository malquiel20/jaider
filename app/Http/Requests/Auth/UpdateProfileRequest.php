<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'first_name' => ['sometimes', 'required', 'string', 'max:100'],
            'last_name' => ['sometimes', 'nullable', 'string', 'max:100'],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['sometimes', 'required', 'string', 'min:8', 'confirmed'],
            'phone_number' => ['sometimes', 'nullable', 'string', 'regex:/^\+?[0-9]{7,15}$/', Rule::unique('users', 'phone_number')->ignore($userId)],
        ];
    }

    public function messages(): array
    {
        return UserMessages::all();
    }
}
