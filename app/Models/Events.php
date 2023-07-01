<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    use HasFactory;

    protected $fillable = [
        "id",
        "title",
        "content",
        "short_description",
        "location_name",
        "location_lon",
        "location_lat",
        "start_at",
        "end_at",
        "created_by",
        "created_at",
        "updated_at",
        "status"
    ];

    protected $attributes = [
        "status" => "pending"
    ];

    protected $hidden = ["created_at", "updated_at"];

    public function getEvents(){
        return $this->where('status', '!=', 'deleted')->get();
    }
}
