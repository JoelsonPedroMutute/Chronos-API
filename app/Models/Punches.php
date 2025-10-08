<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
use App\Models\Companies;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Punches extends Model
{
    use HasFactory;

     /** @use HasFactory<\Database\Factories\PunchesFactory> */ 
     
    protected $fillable = [
        'type',
        'punch_time',
        'auto_closed',
        'extra_time',
        'note',
        'employee_id',
        'company_id',
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
