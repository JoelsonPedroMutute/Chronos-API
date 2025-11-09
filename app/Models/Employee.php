<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Companies;
use App\Models\Employee_Categories;
use App\Models\Punches;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Testing\Fluent\Concerns\Has;

/**
 * @mixin IdeHelperEmployee
 */
class Employee extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /** @use HasFactory<\Database\Factories\EmployeeFactory> */

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'address',
        'position',
        'settings',
        'hire_date',
        'role',
        'salary',
        'status',
        'department',
        'image',
        'company_id',
        'employee_category_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
 public function getProfileImageAttribute()
{
    // Carrega o relacionamento 'user' se ainda não estiver carregado
    if (!$this->relationLoaded('user')) {
        $this->load('user');
    }

    // Se existir um user associado com imagem, retorna a imagem dele
    if ($this->user && !empty($this->user->image)) {
        // Se o caminho for relativo (sem "http"), gera URL pública
        return str_starts_with($this->user->image, 'http')
            ? $this->user->image
            : asset('storage/' . $this->user->image);
    }

    // Caso contrário, retorna a imagem própria do employee (se tiver)
    if (!empty($this->image)) {
        return str_starts_with($this->image, 'http')
            ? $this->image
            : asset('storage/' . $this->image);
    }

    // Se nenhum tiver imagem, usa padrão
    return asset('images/default-employee.png');
}


    public function company()
    {
        return $this->belongsTo(Companies::class);
    }
    public function employeeCategory()
    {
        return $this->belongsTo(EmployeeCategory::class);
    }
    public function punches()
    {
        return $this->hasMany(Punches::class);
    }
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }
}
