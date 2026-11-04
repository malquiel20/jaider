<?php

namespace App\Http\Requests\Contacts;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateContactRequest extends FormRequest
{
    /**
     * Antes de validar, se revisa que el contacto sea del usuario (ContactPolicy).
     */
    public function authorize(): bool
    {
        Gate::authorize('update', $this->route('contact'));

        return true;
    }

    public function rules(): array
    {
        $contactId = $this->route('contact')->id;

        return [
            'first_name' => ['sometimes', 'required', 'string', 'max:100'],
            'last_name' => ['sometimes', 'nullable', 'string', 'max:100'],
            'phone_number' => ['sometimes', 'required', 'string', 'regex:/^\+?[0-9]{7,15}$/', Rule::unique('contacts', 'phone_number')->ignore($contactId)],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('contacts', 'email')->ignore($contactId)],
            'address' => ['sometimes', 'required', 'string', 'max:255'],
            'avatar_url' => ['sometimes', 'nullable', 'url', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return ContactMessages::all();
    }
}
