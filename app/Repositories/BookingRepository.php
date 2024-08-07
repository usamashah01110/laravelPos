<?php

namespace App\Repositories;

use App\Models\Booking;

class BookingRepository extends BaseRepository
{
    protected $booking;

    public function __construct(Booking $model)
    {
        $this->booking = $model;
        parent::__construct($model);
    }

}
