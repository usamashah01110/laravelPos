<?php

namespace App\Repositories;

use App\Models\userProfile;

class UserProfileRepository extends BaseRepository
{
    protected $userProfile;
    public function __construct(userProfile $model)
    {
        $this->userProfile = $model;
        parent::__construct($model);
    }

}
