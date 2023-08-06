<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Companies extends Model
{
    use HasFactory;

    protected $fillable = [
        "id",
        "image_url",
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
}
