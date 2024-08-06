<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Userprofiles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserGallery extends Model
{
    use HasFactory;

    protected $table = 'user_gallery';

    protected $fillable = [
        'id',
        'alumni_id',
        'image_url',
        'created_at',
        'updated_at'
    ];

    protected $hidden = ['created_at', 'updated_at'];
    

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'alumni_id');
    }

    /**
     * Get the user profile associated with the user gallery.
     */
    public function userProfile(): BelongsTo
    {
        return $this->belongsTo(Userprofiles::class, 'alumni_id');
    }
}
