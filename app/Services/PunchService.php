<?php

namespace App\Services;

use App\Filters\PunchFilter;
use App\Models\Punch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PunchService
{
    public function getAll(Request $request)
    {
        $query = Punch::query();
        $filter = new PunchFilter($query, $request);
        $filtered = $filter->applySearcchFilter();

        return $request->query('paginate') === 'false'
            ? $filtered->get()
            : $filtered->paginate($request->query('per_page', 10));
    }

    public function findById(int $id): Punch
    {
        if (!$punch = Punch::find($id)) {
            throw new ModelNotFoundException('Punch não encontrado');
        }
        return $punch;
    }

    public function create(array $data): Punch
    {
        if (!$punch = Punch::create($data)) {
            throw new ModelNotFoundException('Punch não criado');
        }
        return $punch;
    }

    public function update(array $data, Punch $punch): Punch
    {
        if (!$punch->update($data)) {
            throw new ModelNotFoundException('Punch não atualizado');
        }
        return $punch->fresh();
    }

    public function delete(Punch $punch): bool
    {
        if (!$punch->delete()) {
            throw new ModelNotFoundException('Punch não deletado');
        }
        return true;
    }
}