<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class UpdateUserRequest extends FormRequest
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
            'name'=>'sometimes|string|max:255',
            'email'=> 'sometimes|string|email|max:255|unique:users',
            'phone_number'=> 'nullable|string|max:20',
            'role'=>'sometimes|string|in:superadmin,admin,manager,user',
            'status'=>'sometimes|string|in:active,inactive',
            'image'=>'sometimes|image|mimes:jpg,jpeg,png|max:2048',
            'has_image'=>'sometimes|boolean',
            'password'=>'sometimes|string|min:8|confirmed',
        ];
    }
     public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'O campo email deve ser um endereço de email válido.',
            'email.unique' => 'O email informado já está em uso.',
            'role.required' => 'O campo função é obrigatório.',
            'role.in' => 'O campo função deve ser um dos seguintes valores: superadmin, admin, manager, user.',
            'status.required' => 'O campo status é obrigatório.',
            'status.in' => 'O campo status deve ser um dos seguintes valores: active, inactive.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.confirmed' => 'A confirmação da senha não corresponde.',
        ];
    }   
}
