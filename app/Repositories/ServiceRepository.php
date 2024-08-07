<?php

namespace App\Repositories;

use App\Models\Clinics;
use App\Models\Service;

class ServiceRepository extends BaseRepository
{
    protected $service;
    public function __construct(Service $model)
    {
        $this->service = $model;
        parent::__construct($model);
    }

    public function search($keyword, $latitude = null, $longitude = null, $radius = 10)
    {
        $clinics = Clinics::select('id')
            ->selectRaw("( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance", [$latitude, $longitude, $latitude])
            ->having('distance', '<', $radius)
            ->pluck('id');

        return $this->model->whereIn('clinics_id', $clinics)
            ->where('services.name', 'LIKE', "%{$keyword}%")
            ->orWhereHas('category', function ($query) use ($keyword) {
                $query->where('name', 'LIKE', "%{$keyword}%");
            })
            ->get();
    }
}
