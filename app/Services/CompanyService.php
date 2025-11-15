<?php

namespace App\Services;

use App\Models\Company;
use Exception;
use Illuminate\Http\Request;
use App\Filters\CompanyFilters;
use App\Models\Companies;
use Illuminate\Database\QueryException;

class CompanyService
{
    public function getAll(Request $request)
    {
        $query = Companies::query();
        $filter = new CompanyFilters($query, $request);
        $filtered = $filter->applySearcchFilter();

        $perPage = (int) $request->get('per_page', 10);
        return $filtered->paginate($perPage);
    }

    public function findById(string $id): ?Companies
    {
        return Companies::withTrashed()->find($id);
    }

    public function create(array $data)
    {
        try{
            if(Companies::where('name', $data['name'])->exists()){
                throw new Exception('já existe uma empres com este nome.');
            }
        return Companies::create($data);
    } catch (QueryException $e){
        $errorCode = $e->getCode();
        $messages = [
            '23505' => 'Já existe uma empresa registrada com o mesmo NIF ou email.',
        ];
    }
    throw new Exception($messages[$errorCode] ?? 'Erro ao criar a emoresa.');
}

    public function update(Companies $company, array $data)
    {
        if (
            isset($data['name']) &&
            $data['name'] !== $company->name &&
            Companies::where('name', $data['name'])->exists()
        ) {
            throw new Exception('Já existe uma empresa com este nome.');
        }

        $company->update($data);
        return $company->fresh();
    }

    public function delete(Companies $company): bool
    {
        if ($company->trashed()) {
            throw new Exception('A empresa já foi removida anteriormente.');
        }

        return $company->delete();
    }
     public function restore(Companies $company): bool
    {
        if (!$company->trashed()) {
            throw new Exception('A empresa não foi removida anteriormente.');
        }

        return $company->restore();
    }
}
