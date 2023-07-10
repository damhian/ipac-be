<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Storemedia extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'id',
        'store_id',
        'filename',
        'short_description',
        'created_by',
        'created_at',
        'updated_at'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function store(){
        return $this->belongsTo(Store::class);
    }
}
