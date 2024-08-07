<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Usergallery;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserProfiles extends Model
{
    use HasFactory;

    protected $table = 'user_profiles';

    protected $fillable = [
        'id',
        'alumni_id',
        'nomor_anggota',
        'license_number',
        'profile_image_id',
        'first_name',
        'last_name',
        'tahun_masuk',
        'tahun_lulus',
        'training_program',
        'batch',
        'current_job',
        'current_workplace',
        'birth_place',
        'date_of_birth',
        'nationality',
        'address',
        'phone_number',
        'phone_number_code',
        'gender',
        'created_at',
        'updated_at',
    ];

    protected $attributes = ['nationality' => 'INDONESIA'];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Get the user associated with the user profile.
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'alumni_id');
    }

    /**
     * Get the user gallery associated with the user profile.
     */
    
    public function userExperiences():HasMany {
        return $this->hasMany(Userexperiences::class, 'alumni_id', 'alumni_id');
    }
        
    public function userGallery(): HasOne
    {
        return $this->hasOne(Usergallery::class, 'alumni_id', 'alumni_id');
    }

    public function userIdcards(): HasOne
    {
        return $this->hasOne(Idcard::class, 'alumni_id', 'alumni_id');
    }
}
