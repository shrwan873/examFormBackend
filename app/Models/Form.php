<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    protected $fillable = ['title','description','exam_date','fee','structure'];
    protected $casts = [
        'structure' => 'array',
        'exam_date' => 'date'
    ];

    public function submissions() 
    { 
        return $this->hasMany(Submission::class); 
    }
}
