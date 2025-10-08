<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Companies;
use App\Models\Employee_Categories;
use App\Models\Punches;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Testing\Fluent\Concerns\Has;

class Employee extends Model
{
    use HasFactory, HasUuids;

     /** @use HasFactory<\Database\Factories\EmployeeFactory> */    

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'address',
        'position',
        'settings',
        'company_id',
        'employee_category_id',
    ];

    public function company()
    {
        return $this->belongsTo(Companies::class);
    }
    public function employeeCategory()
    {
        return $this->belongsTo(Employee_Categories::class);
    }
    public function punches()
    {
        return $this->hasMany(Punches::class);
    }
}
