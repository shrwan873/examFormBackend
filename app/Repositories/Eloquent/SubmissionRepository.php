<?php

namespace App\Repositories\Eloquent;

use App\Models\Submission;
use App\Repositories\Interfaces\SubmissionRepositoryInterface;

class SubmissionRepository implements SubmissionRepositoryInterface
{
    public function create(array $data)
    {
        return Submission::create($data);
    }
    public function find($id)
    {
        return Submission::find($id);
    }
    public function forUser($userId)
    {
        return Submission::with('form', 'payment')->where('user_id', $userId)->get();
    }
    public function all()
    {
        return Submission::with('user', 'form', 'payment')->get();
    }
}
