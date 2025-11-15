<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Punch;

class ValidPunchSequence implements ValidationRule
{
    protected string $employeeId;
    protected string $type;
    protected ?int $currentPunchId;

    public function __construct(string $employeeId, string $type, ?int $currentPunchId = null)
    {
        $this->employeeId = $employeeId;
        $this->type = $type;
        $this->currentPunchId = $currentPunchId; // ID do punch que está sendo atualizado (ou null se for novo)
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Buscar o último punch do funcionário, ignorando o punch atual se estiver atualizando
        $lastPunchQuery = Punch::where('employee_id', $this->employeeId)
            ->orderBy('punch_time', 'desc')
            ->orderBy('id', 'desc');

        if ($this->currentPunchId) {
            $lastPunchQuery->where('id', '!=', $this->currentPunchId);
        }

        $lastPunch = $lastPunchQuery->first();

        // 1️⃣ Primeiro registro deve ser 'in'
        if (!$lastPunch) {
            if ($this->type !== 'in') {
                $fail('O primeiro registro deve ser de entrada (in).');
            }
            return;
        }

        $lastType = $lastPunch->type;

        // 2️⃣ Bloquear múltiplos "in" consecutivos (ou sem "out" anterior)
        if ($this->type === 'in' && in_array($lastType, ['in', 'break_start', 'break_end'])) {
            $fail("Você não pode registrar 'in' novamente antes de um 'out'.");
            return;
        }

        // 3️⃣ Bloquear duplicados diretos idênticos (ex: out -> out)
        if ($this->type === $lastType) {
            $fail("Não é permitido registrar dois '{$this->type}' consecutivos.");
            return;
        }

        // 4️⃣ Sequência permitida
        $allowedNext = match ($lastType) {
            'in' => ['out', 'break_start'],
            'out' => ['in'],
            'break_start' => ['break_end'],
            'break_end' => ['out', 'break_start'],
            default => [],
        };

        if (!in_array($this->type, $allowedNext)) {
            $expected = implode(' ou ', $allowedNext);
            $fail("Após um registro de '{$lastType}', o próximo deve ser '{$expected}'.");
        }
    }
}
