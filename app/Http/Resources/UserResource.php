<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'role' => $this->role,
            'status' => $this->status,
            'image' => $this->image ? asset('storage/'.$this->image) : null,
            'has_image' => !is_null($this->image),

            // 👇 Aqui adicionamos o empregado relacionado
            'employee' => $this->whenLoaded('employee', function () {
                return [
                    'id' => $this->employee->id,
                    'first_name' => $this->employee->first_name,
                    'last_name' => $this->employee->last_name,
                    'email' => $this->employee->email,
                    'phone' => $this->employee->phone_number,
                    'address' => $this->employee->address,
                    'hire_date' => $this->employee->hire_date,
                    'category' => $this->employee->employeeCategory?->name,
                    'company' => [
                        'id' => $this->employee->company?->id,
                        'name' => $this->employee->company?->name,
                        'address' => $this->employee->company?->address,
                        'phone' => $this->employee->company?->phone,
                        'email' => $this->employee->company?->email,
                    ],
                ];
            }),
        ];
    }
}
