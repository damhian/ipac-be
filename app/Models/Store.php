<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $table = 'store';

    protected $fillable = [
        'id',
        'title',
        'content',
        'short_description',
        'price',
        'created_by',
        'created_at',
        'updated_at',
        'status'
    ];

    protected $attributes = ['status' => 'pending'];

    protected $hidden = ['created_at', 'updated_at'];

    public function storeMedia(){
        return $this->hasMany(Storemedia::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
