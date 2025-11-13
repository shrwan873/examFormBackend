<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['user_id','submission_id','amount','status','gateway','transaction_id','meta'];
    protected $casts = ['meta' => 'array'];

    public function user() 
    { 
        return $this->belongsTo(User::class); 
    }
    public function submission() 
    { 
        return $this->belongsTo(Submission::class); 
    }
}
