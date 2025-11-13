<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SubmissionService;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    protected $service;
    public function __construct(SubmissionService $service)
    {
        $this->service = $service;
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'form_id' => 'required|exists:forms,id',
            'answers' => 'required|array'
        ]);
        $submission = $this->service->submit([
            'user_id' => auth()->id(),
            'form_id' => $data['form_id'],
            'answers' => $data['answers'],
            'status' => 'pending'
        ]);
        return response()->json($submission, 201);
    }

    public function mySubmissions()
    {
        return response()->json($this->service->getForUser(auth()->id()));
    }
    public function index()
    {
        return response()->json($this->service->all());
    }
    public function show($id)
    {
        return response()->json($this->service->find($id));
    }
}
