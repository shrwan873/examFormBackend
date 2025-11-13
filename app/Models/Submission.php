<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = ['user_id','form_id','answers','status'];
    protected $casts = ['answers' => 'array'];

    public function user() 
    { 
        return $this->belongsTo(User::class); 
    }
    public function form() 
    { 
        return $this->belongsTo(Form::class); 
    }
    public function payment() 
    { 
        return $this->hasOne(Payment::class); 
    }
}
