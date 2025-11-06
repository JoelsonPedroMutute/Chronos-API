<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'nif' => [
                'sometimes','string',
                Rule::unique('companies', 'nif')->ignore($this->company)
            ],
            'email' => [
                'sometimes','string','email','max:255',Rule::unique('companies', 'email')->ignore($this->company)
            ],
            'phone_number' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'nif.unique' => 'Este NIF já pertence a uma empresa.',
            'email.unique' => 'Este email já pertence a uma empresa.',
        ];
    }
}
