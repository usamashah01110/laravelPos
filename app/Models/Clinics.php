<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clinics extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'owner_id',
        'image_urls',
        'whats_app',
        'website',
        'instagram_link',
        'facebook_link',
        'address',
        'created_by',
        'updated_by',
    ];

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    // Define the clinicHours relationship
    public function clinicHours()
    {
        return $this->hasMany(ClinicHours::class);
    }

    public function service()
    {
        return $this->hasMany(Service::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
