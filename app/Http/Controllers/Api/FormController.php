<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FormService;
use Illuminate\Http\Request;

class FormController extends Controller
{
    protected $service;
    public function __construct(FormService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json($this->service->list());
    }
    public function show($id)
    {
        return response()->json($this->service->get($id));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'exam_date' => 'nullable|date',
            'fee' => 'required|numeric',
            'structure' => 'nullable|array'
        ]);
        return response()->json($this->service->create($data), 201);
    }

    public function update(Request $r, $id)
    {
        $data = $r->only(['title', 'description', 'exam_date', 'fee', 'structure']);
        return response()->json($this->service->update($id, $data));
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return response()->json(['message' => 'deleted successfully.']);
    }
}
