<?php

namespace App\Http\Requests\Contacts;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'phone_number' => ['required', 'string', 'regex:/^\+?[0-9]{7,15}$/', 'unique:contacts,phone_number'],
            'email' => ['required', 'email', 'max:255', 'unique:contacts,email'],
            'address' => ['required', 'string', 'max:255'],
            'avatar_url' => ['nullable', 'url', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return ContactMessages::all();
    }
}
