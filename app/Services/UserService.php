<?php

namespace App\Services;

use App\Models\User;
use App\Filters\UserFilter;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

use function Laravel\Prompts\password;

class UserService
{
    public function getAllFiltered(Request $request)
    {
        $query = User::with(['employee.company', 'employee.employeeCategory']); // carrega os relacionamentos

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
    public function changeRole(User $user, array $data): User
    {
        $user->update([
            'role' => $data['role'],
        ]);
        return $user->fresh();
    }
    public function sendPasswordResetEmail(string $email): void
    {
        $user = User::where('email', $email)->firstOrFail();
        $user->sendPasswordResetNotification(
            $user->createToken('reset_token')->plainTextToken
        );
    }
    public function resetPassword(array $data): void
    {
        $status = Password::reset($data, function ($user, $password) {
            $user->password = bcrypt($password);
            $user->save();

            $user->tokens()->delete();
        });
        if ($status !== Password::PASSWORD_RESET) {
            throw new Exception('Erro ao redefinir a senha.');
        }
    }
    public function changePassword(string $id, array $data): void
    {
        $user = User::findOrFail($id);
        $user->update([
            'password' => bcrypt($data['password']),
        ]);
        $user->tokens()->delete();
    }
    public function destroy(string $id): void
    {
        $user = User::findOrFail($id);
        $user->delete();
    }
    public function delete(string $id): void
    {
        $user = User::findOrFail($id);
        $user->delete();
    }
}
