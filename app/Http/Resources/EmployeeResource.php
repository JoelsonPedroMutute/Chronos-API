<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone_number,
            'image' => $this->image ? asset('storage/'.$this->image) : null,
            'has_image' => !is_null($this->image),
            'address' => $this->address,
            'hire_date' => $this->hire_date,
            'category' => $this->employeeCategory?->name,
            'company' => [
                'id' => $this->company?->id,
                'name' => $this->company?->name,
                'address' => $this->company?->address,
                'phone' => $this->company?->phone,
                'email' => $this->company?->email,
            ],
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'role' => $this->user->role,
                'status' => $this->user->status,
            ] : null,
        ];
    }
}
