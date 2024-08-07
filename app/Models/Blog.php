<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Blog extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $dates = ['deleted_at'];

    public function setSlugAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['slug'] = Str::slug($value, '-');
        } else {
            $this->attributes['slug'] = Str::slug($this->title, '-');
        }
    }
}
