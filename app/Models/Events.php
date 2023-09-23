<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Events extends Model
{
    use HasFactory;

    protected $fillable = [
        "id",
        "title",
        "content",
        "image",
        "short_description",
        "location_name",
        "location_lon",
        "location_lat",
        "start_at",
        "end_at",
        "event_time",
        "type",
        "created_by",
        "created_at",
        "updated_at",
        "status"
    ];

    protected $attributes = [
        "status" => "pending"
    ];

    protected $hidden = ["created_at", "updated_at"];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
}
