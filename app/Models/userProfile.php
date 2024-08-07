<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class userProfile extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'gender',
        'image',
        'address',
        'emergency_phone',
        'facebook',
        'instagram',
        'twitter',
        'about_me',
        'hobbies',
        'job_title',
        'job_experience',
        'education_history',
        'professional_certification',
        'profile_status',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
