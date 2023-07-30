<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Idcard extends Model
{
    use HasFactory;

    protected $table = 'id_cards';

    protected $fillable = [
        'id',
        'alumni_id',
        'nomor_anggota',
        'first_name',
        'last_name',
        'image_url',
        'created_at',
        'updated_at'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'alumni_id');
    }
}
