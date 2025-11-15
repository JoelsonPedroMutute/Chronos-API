<?php

namespace App\Http\Requests;

use App\Rules\ValidPunchSequence;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePunchRequest extends FormRequest
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
        'employee_id' => 'sometimes|string|exists:employees,id',
        'company_id' => 'sometimes|integer|exists:companies,id',
        'type' => [
            'sometimes',
            'string',
            'in:in,out,break_start,break_end',
            new ValidPunchSequence(
                $this->input('employee_id', $this->route('punch')->employee_id ?? ''),
                $this->input('type', ''),
                $this->route('punch')->id // <-- ID do punch que está sendo atualizado
            ),
        ],
        'punch_time' => 'sometimes|date_format:Y-m-d H:i:s',
        'auto_closed' => 'sometimes|boolean',
        'extra_time' => 'nullable|numeric|min:0',
        'note' => 'nullable|string|max:500',
    ];
}

    public function messages(): array
    {
        return [
            'employee_id.exists' => 'Funcionário não encontrado.',
            'company_id.exists' => 'Empresa não encontrada.',
            'type.in' => 'O tipo deve ser: in, out, break_start ou break_end.',
            'punch_time.date_format' => 'A data/hora deve estar no formato: YYYY-MM-DD HH:MM:SS (ex: 2025-11-12 08:30:00).',
            'auto_closed.boolean' => 'O campo auto_closed deve ser true ou false.',
            'extra_time.numeric' => 'O tempo extra deve ser um número.',
            'extra_time.min' => 'O tempo extra não pode ser negativo.',
            'note.max' => 'A nota não pode ter mais de 500 caracteres.',
        ];
    }
}