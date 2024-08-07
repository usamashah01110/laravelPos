<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'service_id'
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Define the relationship with the Service model
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
