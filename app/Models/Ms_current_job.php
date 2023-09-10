<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ms_current_job extends Model
{
    use HasFactory;

    protected $table = "ms_current_job";

    protected $fillable = [
        "id",
        "job",
        "created_at",
        "updated_at"
    ];

    protected $hidden = ["created_at", "updated_at"];
}
