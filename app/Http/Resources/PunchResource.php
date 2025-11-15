<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PunchResource extends JsonResource
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
            'type' => $this->type,
            'punch_time' => $this->punch_time ? $this->punch_time->format('Y-m-d H:i:s') : null,
            'auto_closed' =>(bool) $this->auto_closed,
            'extra_time' => $this->extra_time ? (float) $this->extra_time : 0,
            'note' => $this->note,
            'employee_id' => $this->employee_id,
            'company_id' => $this->company_id,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
