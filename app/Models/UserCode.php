<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCode extends Model
{
    use HasFactory;

    public $timestamps= false;
    protected $table = 'user_codes';

    protected $fillable = [
        'email',
        'code',
        'otp',
        'phone',
        'expired_at',
        'created_at'
    ];
}
