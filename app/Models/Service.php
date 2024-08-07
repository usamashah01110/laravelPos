<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'price', 'clinics_id', 'category_id' , 'created_by' ,'updated_by', 'image_url'
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinics::class, 'clinics_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function teams()
    {
        return $this->hasMany(Team::class, 'service_id');
    }

}
