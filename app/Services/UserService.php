<?php

namespace App\Services;

use App\Models\User;
use App\Filters\UserFilter;
use Illuminate\Http\Request;

class UserService
{
    public function getAllFiltered(Request $request)
    {
        $query = User::query();
        $filter = new UserFilter($query, $request);
        
        return $filter->apply()->paginate($request->input('per_page', 10));
    }
}