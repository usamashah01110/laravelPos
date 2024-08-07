<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'twillio',
        'twillio_client_id',
        'twillio_secret_id',
        'twoFactor',
        'twoFactor_type',
        'payment_method_type',
        'payment_method_secret_id',
        'payment_method_client_id',
    ];
}
