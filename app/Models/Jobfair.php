<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jobfair extends Model
{
    use HasFactory;
    
    protected $table = 'jobfair';

    protected $fillable = [
        'id',
        'title',
        'content',
        'short_description',
        'region',
        'company',
        'jobtype',
        'jobtitle',
        'location_name',
        'location_lon',
        'location_lat',
        'start_at',
        'end_at',
        'created_by',
        'created_at',
        'updated_at',
        'status'
    ];

    protected $attributes = [
        'status' => 'pending',
        'jobtype' => 'Waktu Penuh'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function getJobfairs() {
        return $this->where('status', '!=', 'deleted')->get();
    }

    public function user() {
        return $this->belongsTo(User::class, 'created_by');
    }

}
