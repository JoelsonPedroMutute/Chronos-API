<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Aqui podemos integrar com a policy
        return $this->user()?->can('create', \App\Models\EmployeeCategory::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:employee_categories,code',
            'company_id' => 'sometimes|integer|exists:companies,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da categoria é obrigatório.',
            'code.unique' => 'O código informado já está em uso.',
            'company_id.exists' => 'A empresa informada não existe.',
        ];
    }
}
