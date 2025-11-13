<?php

namespace App\Services;

use App\Repositories\Interfaces\SubmissionRepositoryInterface;

class SubmissionService
{
    protected $repo;
    public function __construct(SubmissionRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function submit(array $data)
    {
        return $this->repo->create($data);
    }

    public function getForUser($userId)
    {
        return $this->repo->forUser($userId);
    }
    public function all()
    {
        return $this->repo->all();
    }
    public function find($id)
    {
        return $this->repo->find($id);
    }
}
