<?php

namespace App\Repositories\Eloquent;

use App\Models\Form;
use App\Repositories\Interfaces\FormRepositoryInterface;

class FormRepository implements FormRepositoryInterface
{
    public function all()
    {
        return Form::all();
    }
    public function find($id)
    {
        return Form::find($id);
    }
    public function create(array $data)
    {
        return Form::create($data);
    }
    public function update($id, array $data)
    {
        $form = Form::findOrFail($id);
        $form->update($data);
        return $form;
    }
    public function delete($id)
    {
        $form = Form::findOrFail($id);
        return $form->delete();
    }
}
