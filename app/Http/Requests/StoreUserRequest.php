<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'name'=>'required|string|max:255',
            'email'=> 'required|string|email|max:255|unique:users',
            'phone_number'=> 'nullable|string|max:20',
            'role'=>'required|string|in:superadmin,admin,manager,user',
            'status'=>'required|string|in:active,inactive',
            'image'=>'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'has_image'=>'nullable|boolean',
            'password'=>'required|string|min:8|confirmed',
        ];
    }
}
