<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ms_visi_misi extends Model
{
    use HasFactory;

    protected $table = "ms_visi_misi";

    protected $fillable = [
        "id",
        "type",
        "content",
        "created_at",
        "updated_at"
    ];

    protected $hidden = ["created_at", "updated_at"];
}
