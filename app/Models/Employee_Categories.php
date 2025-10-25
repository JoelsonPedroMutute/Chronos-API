<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Companies;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperEmployee_Categories
 */
class Employee_Categories extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'code',
        'settings',
        'company_id',
    ];

    public function company()
    {
        return $this->belongsTo(Companies::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }


}
