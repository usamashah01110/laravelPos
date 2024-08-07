<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicHours extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinics_id',
        'start_time',
        'end_time',
        'day_of_week',
        'is_open',
        'created_by',
    ];

    public function clinic()
    {
        return $this->belongsTo(Clinics::class);
    }
}
