<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Userexperiences extends Model
{
    use HasFactory;

    protected $table = 'user_experiences';

    protected $fillable = [
        'id',
        'alumni_id',
        'company_id',
        'position',
        'start_at',
        'end_at',
        'created_at',
        'updated_at'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function user() 
    {
        return $this->belongsTo(User::class, 'alumni_id');
    }
}
