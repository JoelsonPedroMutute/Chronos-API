<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && ($user->hasRole('superadmin') || $user->hasRole('admin'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'nullable|exists:users,id|unique:employees,user_id',
            'create_user' => 'nullable|boolean',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:employees',
            'hire_date' => 'required|date|before_or_equal:today',
            'status' => 'required|string|in:active,inactive,on_leave',
            'salary' => 'required|numeric|min:0',
            'role' => 'nullable|required_if:create_user,true|string|in:superadmin,admin,manager,user',
            'settings' => 'nullable|json',
            'password' => 'nullable|required_if:create_user,true|string|min:8|confirmed',
            'phone_number' => 'nullable|string|max:20',
            'position' => 'required|string|max:100',
            'employee_category_id' => 'nullable|exists:employee_categories,id',
            'address' => 'nullable|string|max:500',
            'company_id' => 'nullable|exists:companies,id',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'O campo nome é obrigatório.',
            'last_name.required' => 'O campo sobrenome é obrigatório.',
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'O campo email deve ser um endereço de email válido.',
            'email.unique' => 'O email informado já está em uso.',
            'hire_date.required' => 'O campo data de contratação é obrigatório.',
            'status.required' => 'O campo status é obrigatório.',
            'status.in' => 'O campo status deve ser um dos seguintes valores: active, inactive, on_leave.',
            'salary.required' => 'O campo salário é obrigatório.',
            'salary.numeric' => 'O campo salário deve ser um número.',
            'salary.min' => 'O campo salário deve ser um valor positivo.',
            'role.required' => 'O campo função é obrigatório.',
            'role.in' => 'O campo função deve ser um dos seguintes valores: superadmin, admin, manager, user.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.confirmed' => 'A confirmação da senha não corresponde.',
            'employee_category_id.exists' => 'A categoria de empregado selecionada é inválida.',
            'company_id.exists' => 'A empresa selecionada é inválida.',
        ];
    }
}
