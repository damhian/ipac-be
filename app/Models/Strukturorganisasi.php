<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Strukturorganisasi extends Model
{
    use HasFactory;

    protected $table = 'struktur_organisasi';

    protected $fillable = [
        'id',
        'nama',
        'jabatan',
        'image_url',
        'created_by',
        'created_at',
        'updated_at'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
