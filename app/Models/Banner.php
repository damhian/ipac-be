<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $table = 'banner';

    protected $fillable = [
        "id",
        "title",
        "content",
        "short_description",
        "image",
        "created_by",
        "created_at",
        "updated_at",
        "status"
    ];

    protected $attributes = [
        "status" => "active"
    ];

    protected $hidden = ["created_at", "updated_at"];
}
