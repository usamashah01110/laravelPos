<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface FilterInterface
{
    public function get_filters(Request $request);

    public function apply_filters($filterParameters);
}
