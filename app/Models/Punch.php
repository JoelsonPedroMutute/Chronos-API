<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Punch extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'company_id',
        'type',
        'punch_time',
        'auto_closed',
        'extra_time',
        'note',
    ];

    protected $casts = [
        'punch_time' => 'datetime',
        'auto_closed' => 'boolean',
        'extra_time' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function company()
    {
        return $this->belongsTo(Companies::class);
    }
}