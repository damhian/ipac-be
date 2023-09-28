<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\NewResetPasswordNotification;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'username',
        'email',
        'password',
        'role',
        'status',
        'current_status',
        'created_at',
        'updated_at'
    ];

    /**
     * Default values for attributes
     * @var  array an array with attribute as key and default as value
     */

    protected $attributes = [
        'role' => 'alumni',
        'current_status' => 'HIDUP'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at'
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isAdmin(){
        return ($this->role === 'admin') || ($this->role === 'superadmin');
    }
    
    public function isSuperadmin(){
        return ($this->role === 'superadmin');
    }

    public function isApproved(){
        return $this->status === 'approved';
    }

    public function userExperience():HasMany {
        return $this->hasMany(Userexperiences::class, 'alumni_id');
    }

    public function userProfiles():HasOne {
        return $this->hasOne(Userprofiles::class, 'alumni_id');
    }
    
    public function userIdcards():HasOne {
        return $this->hasOne(Idcard::class, 'alumni_id');
    }

    public function userGallery():HasOne {
        return $this->hasOne(Usergallery::class, 'alumni_id');
    }

    public function userEvents():HasMany {
        return $this->hasMany(Events::class, 'created_by');
    }

    public function userJobfair():HasMany {
        return $this->hasMany(Jobfair::class, 'created_by');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new NewResetPasswordNotification($token));
    }
}
