<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagePermission extends Model
{
    protected $fillable = ['user_id', 'page_slug'];

    /**
     * Get the user that owns the page permission.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
