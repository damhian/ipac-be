<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Companies extends Model
{
    use HasFactory;

    protected $fillable = [
        "id",
        "name",
        "about",
        "created_by",
        "created_at",
        "updated_at"
    ];

    protected $hidden = ["created_at", "updated_at"];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function userExperience()
    {
        return $this->belongsTo(Userexperiences::class, 'company_id', 'id');
    }
}
