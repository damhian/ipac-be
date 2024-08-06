<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ms_status extends Model
{
    use HasFactory;

    protected $table = "ms_status";

    protected $fillable = [
        "id",
        "status",
        "created_at",
        "updated_at"
    ];

    protected $hidden = ["created_at", "updated_at"];
}
