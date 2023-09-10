<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ms_training_program extends Model
{
    use HasFactory;

    protected $table = "ms_training_program";

    protected $fillable = [
        "id",
        "training_program",
        "created_at",
        "updated_at"
    ];

    protected $hidden = ["created_at", "updated_at"];
}
