<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $category = $this->route('employee_category');
        return $this->user()?->can('update', $category) ?? false;
    }

    public function rules(): array
    {
        $categoryId = $this->route('employee_category')?->id;

        return [
            'name' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|max:10|unique:employee_categories,code,' . $categoryId,
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório quando informado.',
            'code.unique' => 'O código já está em uso por outra categoria.',
        ];
    }
}
