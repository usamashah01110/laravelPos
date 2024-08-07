<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Helpers\ApiResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\UserAdded;
use App\Notifications\UserEdited;
use App\Notifications\UserDeleted;
use App\Services\FilterServices;
use Illuminate\Support\Facades\Schema;

class  UserController extends BaseController
{

    private $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        parent::__construct($this->userRepository, "User");
    }

    protected function validation_on_creation(Request $request,$id=false)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                isset($id) ? Rule::unique($this->repository->getTableName())->ignore($id) : Rule::unique($this->repository->getTableName()),
            ],
            'password' => 'sometimes|required|string|max:255',
            'phone' => [
                'sometimes',
                'required',
                'numeric',
                isset($id) ? Rule::unique($this->repository->getTableName())->ignore($id) : Rule::unique($this->repository->getTableName()),
            ],
        ]);
            if ($validator->validate()) {
                    $user = Auth::user();
                    $data = $request->all();
                    return $data;
            }
    }

    protected function _pre_save($data)
    {
        return $data;
    }

    protected function _post_save($data)
    {
        return $data;
    }


}

