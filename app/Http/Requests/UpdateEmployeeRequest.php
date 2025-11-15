<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $employee = $this->route('employee');

        if (!$user || !$employee) {
            return false;
        }

        // SuperAdmin pode tudo
        if ($user->hasRole('superadmin')) {
            return true;
        }

        // Admin pode tudo, exceto alterar SuperAdmins
        if ($user->hasRole('admin')) {
            return !$employee->user || !$employee->user->hasRole('superadmin');
        }

        // Manager e User só podem atualizar o próprio perfil
        return $user->employee && $user->employee->id === $employee->id;
    }

    /**
     * Regras de validação dinâmicas com base no papel do usuário.
     */
    public function rules(): array
    {
        $user = $this->user();
        if (!$user) {
            return [];
        }

        // SuperAdmin e Admin – regras completas
        if ($user->hasRole('superadmin') || $user->hasRole('admin')) {
            return [
                'user_id' => 'nullable|exists:users,id|unique:employees,user_id,' . $this->route('employee'),
                'first_name' => 'sometimes|required|string|max:100',
                'last_name' => 'sometimes|required|string|max:100',
                'email' => 'sometimes|required|string|email|max:255|unique:employees,email,' . $this->route('employee'),
                'hire_date' => 'sometimes|required|date',
                'status' => 'sometimes|required|string|in:active,inactive,on_leave',
                'salary' => 'sometimes|required|numeric|min:0',
                'role' => 'sometimes|required|string|in:admin,employee',
                'settings' => 'nullable|json',
                'password' => 'sometimes|required|string|min:8|confirmed',
                'phone_number' => 'nullable|string|max:20',
                'department' => 'sometimes|required|string|max:100',
                'position' => 'sometimes|required|string|max:100',
                'employee_category_id' => 'nullable|exists:employee_categories,id',
                'address' => 'nullable|string|max:500',
                'company_id' => 'nullable|exists:companies,id',
                'image' => 'nullable|image|max:2048',
            ];
        }

        // Manager e User – regras simplificadas
        if ($user->hasRole('manager') || $user->hasRole('user')) {
            return [
                'first_name' => 'sometimes|required|string|max:100',
                'last_name' => 'sometimes|required|string|max:100',
                'email' => 'sometimes|required|string|email|max:255|unique:employees,email,' . $this->route('employee'),
                'password' => 'sometimes|required|string|min:8|confirmed',
                'phone_number' => 'sometimes|required|string|max:20',
                'address' => 'sometimes|required|string|max:500',
                'image' => 'nullable|image|max:2048',
            ];
        }

        return [];
    }

    /**
     * Custom error messages for validation.
     *
     * @return array<string, string>
     */
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
            'role.in' => 'O campo função deve ser um dos seguintes valores: admin, employee.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.confirmed' => 'A confirmação da senha não corresponde.',
            'employee_category_id.exists' => 'A categoria de empregado selecionada é inválida.',
            'company_id.exists' => 'A empresa selecionada é inválida.',
        ];
    }
}
