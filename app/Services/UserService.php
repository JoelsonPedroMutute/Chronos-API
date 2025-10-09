<?php

namespace App\Services;

use App\Models\User;
use App\Filters\UserFilter;
use Exception;
use Illuminate\Http\Request;

class UserService
{
    public function getAllFiltered(Request $request)
    {
        $query = User::query();
        $filter = new UserFilter($query, $request);

        return $filter->apply()->paginate($request->input('per_page', 10));
    }
    public function getById(string $id, UserFilter $filter): User
    {
        $query = User::where('id', $id);
        $filteredQuery = $filter->apply($query);
        return $filteredQuery->firstOrFail();
    }
    public function create(array $data): User
    {
        if (User::where('email', $data['email'])->exists()) {
            throw new Exception('Email já está em uso.');
        }

        return User::create($data);
    }
    public function update(User $user, array $data): User
    {
        if (
            isset($data['email']) &&
            $data['email'] !== $user->email &&
            User::where('email', $data['email'])->exists()
        ) {
            throw new Exception('Email já está em uso.');
        }

        $user->update($data);
        return $user->fresh();
    }
     public function updateStatus(User $user, array $data): User
     {
        $user->update([
            'status' => $data['status'],
        ]);
        return $user->fresh();
    }
}
