<?php

namespace App\Repositories\Interfaces;

interface UserRepositoryInterface
{
    public function find($id);
    public function create(array $data);
    public function findByEmail($email);
}
