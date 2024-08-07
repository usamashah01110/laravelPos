<?php

namespace App\Repositories;

use App\Models\Clinics;
use App\Services\ClinicHoursService;
use Illuminate\Support\Facades\Auth;

class ClinicsRepository extends BaseRepository
{

    protected $clinic;
    public function __construct(Clinics $model)
    {
        $this->clinic = $model;
        parent::__construct($model);
    }

    public function create(array $data)
    {
        $clinic = Clinics::create($data);
//        ClinicHoursService::createDefaultClinicHours($clinic->id, Auth::id());
        return $clinic;
    }
}
