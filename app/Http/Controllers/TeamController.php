<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponseHelper;
use App\Repositories\TeamsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TeamController extends BaseController
{
    public $teamsRepository;
    public function __construct(TeamsRepository $teamsRepository)
    {
        $this->teamsRepository = $teamsRepository;
        parent::__construct($teamsRepository, "Team");
    }


    public function index()
    {
        try {
            $with = ['users','service'];
            $perPage = 10;
            $clinics = $this->teamsRepository->all($with, $perPage);
            $message = 'Clinics retrieved successfully.';
            $data = $clinics;
            return ApiResponseHelper::sendSuccessResponse($data, $message, 200);
        } catch (\Exception $e) {
            return ApiResponseHelper::sendErrorResponse($e->getMessage(), 'Failed to retrieve clinics.', 500);
        }
    }
    protected function validation_on_creation(Request $request,$id=false)
    {

        $validator = Validator::make($request->all(), [
            'clinic_id' => 'sometimes|required|integer|exists:clinics,id',
            'service_id' => 'sometimes|required|string|max:255|exists:services,id',
        ]);
        if ($validator->validate()) {
            $user = Auth::user();
            $data = $request->all();
            $data['created_by'] = $user->id;
            $data['updated_by'] = $user->id;
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

    public function show($id)
    {
        try {
            $with = ['users','service'];
            $data = $this->teamsRepository->get($id,[],$with);
            $message = 'Clinic retrieved successfully.';
            return ApiResponseHelper::sendSuccessResponse($data, $message, 200);
        } catch (\Exception $e) {
            return ApiResponseHelper::sendErrorResponse($e->getMessage(), 'Failed to retrieve clinics.', 500);
        }
    }
}
