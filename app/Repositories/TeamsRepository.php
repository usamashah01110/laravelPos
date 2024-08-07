<?php

namespace App\Repositories;

use App\Models\Team;

class TeamsRepository extends BaseRepository
{

    protected $team;
    public function __construct(Team $model)
    {
        $this->team = $model;
        parent::__construct($model);
    }

}
