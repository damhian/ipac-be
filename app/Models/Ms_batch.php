<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ms_batch extends Model
{
    use HasFactory;

    protected $table = "ms_batch";

    protected $fillable = [
        "id",
        "batch",
        "created_at",
        "updated_at"
    ];
    
    protected $hidden = ["created_at", "updated_at"];
}
