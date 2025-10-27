<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
use App\Models\User;
use App\Models\Employee_Categories;
use App\Models\Punches;
use GuzzleHttp\Psr7\Query;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperCompanies
 */
class Companies extends Model
{
    use HasFactory;

     /** @use HasFactory<\Database\Factories\CompaniesFactory> */

    protected $fillable = [
        'name',
        'nif',
        'email',
        'phone_number',
        'address',
        'timezone',
        'branding',
        'settings',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function employeeCategories()
    {
        return $this->hasMany(EmployeeCategory::class);
    }

    public function punches()
    {
        return $this->hasMany(Punches::class);
    }
    
}
