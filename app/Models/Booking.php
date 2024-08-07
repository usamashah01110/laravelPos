<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
       'owner_id', 'category_id', 'service_id', 'booking_date','team_id', 'booking_time' ,'status', 'created_by' , 'updated_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
