<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidPunchSequence;

class StorePunchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('auto_closed')) {
            $this->merge([
                'auto_closed' => filter_var($this->auto_closed, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            ]);
        }

        if ($this->has('extra_time')) {
            $this->merge([
                'extra_time' => $this->extra_time ? (float) $this->extra_time : null,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'employee_id' => 'required|string|exists:employees,id',
            'company_id' => 'required|integer|exists:companies,id',
            'type' => [
                'required',
                'string',
                'in:in,out,break_start,break_end',
                new ValidPunchSequence(
                    $this->input('employee_id', ''),
                    $this->input('type', '')
                ),
            ],
            'punch_time' => 'required|date_format:Y-m-d H:i:s',
            'auto_closed' => 'nullable|boolean',
            'extra_time' => 'nullable|numeric|min:0',
            'note' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.required' => 'O ID do funcionário é obrigatório.',
            'employee_id.exists' => 'Funcionário não encontrado.',
            'company_id.required' => 'O ID da empresa é obrigatório.',
            'company_id.exists' => 'Empresa não encontrada.',
            'type.required' => 'O tipo de registro é obrigatório.',
            'type.in' => 'O tipo deve ser: in, out, break_start ou break_end.',
            'punch_time.required' => 'A data/hora do registro é obrigatória.',
            'punch_time.date_format' => 'A data/hora deve estar no formato: YYYY-MM-DD HH:MM:SS.',
            'auto_closed.boolean' => 'O campo auto_closed deve ser true ou false.',
            'extra_time.numeric' => 'O tempo extra deve ser um número.',
            'extra_time.min' => 'O tempo extra não pode ser negativo.',
            'note.max' => 'A nota não pode ter mais de 500 caracteres.',
        ];
    }
}