<?php

namespace App\Http\Controllers;

use App\Repositories\ServiceRepository;
use Illuminate\Http\Request;
use App\Helpers\ApiResponseHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ServiceController extends BaseController
{
     protected $serviceRepository;

    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
        parent::__construct($serviceRepository, "Service");
    }

    public function index()
    {
        try {
            $with = ['clinic','category'];

            $perPage = 10;

            $services = $this->serviceRepository->all($with, $perPage);

            $message = 'Services retrieved successfully.';

            return ApiResponseHelper::sendSuccessResponse($services, $message, 200);

        } catch (\Exception $e) {

            return ApiResponseHelper::sendErrorResponse($e->getMessage(), 'Failed to retrieve Services.', 500);
        }
    }

    protected function validation_on_creation(Request $request,$id=false)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'clinics_id' => 'required|exists:clinics,id',
            'category_id' => 'required|exists:categories,id',
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

    public function search(Request $request)
    {
        $this->validate($request, [
            'keyword' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|numeric'
        ]);

        $keyword = $request->input('keyword');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $radius = $request->input('radius');

        $services = $this->serviceRepository->search($keyword, $latitude, $longitude, $radius);

        $message = 'Services Searched successfully.';

        return ApiResponseHelper::sendSuccessResponse($services, $message, 200);

    }

}
