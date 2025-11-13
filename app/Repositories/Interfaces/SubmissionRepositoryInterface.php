<?php

namespace App\Repositories\Interfaces;

interface SubmissionRepositoryInterface
{
    public function create(array $data);
    public function find($id);
    public function forUser($userId);
    public function all();
}
