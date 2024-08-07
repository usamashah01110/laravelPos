<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Helpers\ApiResponseHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserProfileController extends BaseController
{
    protected $userProfileRepository;

    public function __construct(UserRepository $userProfileRepository)
    {
        $this->userProfileRepository = $userProfileRepository;
        parent::__construct($userProfileRepository, "User Profile");
    }

    protected function validation_on_creation(Request $request, $id = false)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->whereNull('deleted_at'), // Ensure user exists and is not soft deleted
            ],
            'gender' => 'sometimes|nullable|string|in:Male,Female,Other', // Adjust based on acceptable values
            'image' => 'sometimes|nullable|string|max:255', // Assuming this is a path or URL
            'address' => 'sometimes|nullable|string|max:255',
            'emergency_phone' => 'sometimes|nullable|string|max:15', // Adjust max length based on your format
            'facebook' => 'sometimes|nullable|url|max:255',
            'instagram' => 'sometimes|nullable|url|max:255',
            'twitter' => 'sometimes|nullable|url|max:255',
            'about_me' => 'sometimes|nullable|string',
            'hobbies' => 'sometimes|nullable|string',
            'job_title' => 'sometimes|nullable|string|max:255',
            'job_experience' => 'sometimes|nullable|string',
            'education_history' => 'sometimes|nullable|string',
            'professional_certification' => 'sometimes|nullable|string',
            'profile_status' => 'sometimes|nullable|string|in:Active,Inactive', // Adjust based on acceptable statuses
        ]);

        $user = Auth::user();
        $data = $request->all();
        $data['created_by'] = $user->id;
        $data['updated_by'] = $user->id;

        return $data;
    }


    protected function _post_save($data)
    {
        return $data;
    }

    protected function _pre_save($data)
    {
        return $data;
    }
//    public function index()
//    {
//        $profiles =  $this->userProfileRepository->getProfilesPaginated(10);
//
//        if ($profiles->isEmpty()) {
//            return response()->json(['error' => 'No profiles found'], 404);
//        }
//
//        return response()->json(['profiles' => $profiles], 200);
//    }



}
