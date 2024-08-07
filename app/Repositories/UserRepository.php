<?php

namespace App\Repositories;
use App\Models\User;

class UserRepository extends BaseRepository
{
    protected $user;
    public function __construct(User $model)
    {
        $this->user = $model;
        parent::__construct($model);
    }
}
