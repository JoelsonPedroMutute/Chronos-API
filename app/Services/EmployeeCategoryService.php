<?php

namespace App\Services;

use App\Models\EmployeeCategory;
use App\Filters\EmployeeCategoryFilter;
use Illuminate\Http\Request;
use Exception;

class EmployeeCategoryService
{
    // public function getAll(Request $request)
    // {
    //     $query = EmployeeCategory::query();

    //     $filter = new EmployeeCategoryFilter($query, $request);
    //     return $filter->apply(); // já retorna paginado
    // }
    public function getAll(Request $request)
    {
        $query = EmployeeCategory::query();
        $filter = new EmployeeCategoryFilter($query, $request);
        $filtered = $filter->apply(); // Retorna Builder

        $perPage = (int) $request->get('per_page', 10);
        return $filtered->paginate($perPage); // ✅ agora é LengthAwarePaginator
    }


    public function create(array $data)
    {
        // Verifica se já existe uma categoria com o mesmo nome
        if (EmployeeCategory::where('name', $data['name'])->exists()) {
            throw new Exception('A categoria já existe.');
        }

        return EmployeeCategory::create($data);
    }


    public function update(EmployeeCategory $category, array $data)
    {
        if (
            isset($data['name']) &&
            $data['name'] !== $category->name &&
            EmployeeCategory::where('name', $data['name'])->exists()
        ) {
            throw new Exception('Já existe uma categoria com este nome.');
        }

        $category->update($data);
        return $category->fresh();
    }


    public function delete(EmployeeCategory $category): bool
    {
        return $category->delete();
    }
}
