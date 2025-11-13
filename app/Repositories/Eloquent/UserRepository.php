<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function find($id)
    {
        return User::find($id);
    }
    public function create(array $data)
    {
        return User::create($data);
    }
    public function findByEmail($email)
    {
        return User::where('email', $email)->first();
    }
}
