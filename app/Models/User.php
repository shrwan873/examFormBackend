<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = ['name','email','password','role'];
    protected $hidden = ['password'];

    public function submissions()
    { 
        return $this->hasMany(Submission::class);
    }
    public function payments() 
    { 
        return $this->hasMany(Payment::class); 
    }

    public function getJWTIdentifier() 
    { 
        return $this->getKey(); 
    }
    public function getJWTCustomClaims() 
    { 
        return []; 
    }
}
