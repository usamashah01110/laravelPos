<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponseHelper;
use Aws\Exception\AwsException;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Repositories\ClinicsRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Nette\Schema\ValidationException;

class ClinicController extends BaseController
{
    protected $clinicRepository;

    public function __construct(ClinicsRepository $clinicRepository)
    {
        $this->clinicRepository = $clinicRepository;
        parent::__construct($clinicRepository, "Clinic");
    }

    public function index()
    {
        try {
            $with = ['owner','ratings', 'clinicHours'];
            $perPage = 10;
            $clinics = $this->clinicRepository->all($with, $perPage);
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
            'image_urls' => 'sometimes|nullable',
            'whats_app' => 'sometimes|nullable|string|max:20',
            'website' => 'sometimes|nullable|url',
            'instagram_link' => 'sometimes|nullable|url',
            'facebook_link' => 'sometimes|nullable|url',
            'address' => 'sometimes|nullable|string|max:255'
        ]);
        if ($validator->validate()) {
            $user = Auth::user();
            $data = $request->all();
            $data['created_by'] = $user->id;
            $data['updated_by'] = $user->id;
            return $data;
        }
    }

    protected function _post_save($data)
    {
        return $data;
    }

    protected function _pre_save($images)
    {
       return store_images_in_S3($images);
    }
//    public function store(Request $request)
//    {
//        try {
//            $input = $this->validation_on_creation($request);
//
//            if ($request->hasFile('image_urls')) {
//                $input['image_urls'] = $this->_pre_save($request->file('image_urls'));
//            }
//
//            $geocodeResult = $this->geocodeAddress($request->address);
//
//            if ($geocodeResult) {
//                $input['latitude'] = $geocodeResult['lat'];
//                $input['longitude'] = $geocodeResult['lng'];
//            } else {
//                return ApiResponseHelper::sendErrorResponse([], "Unable to geocode address'", 400);
//
//            }
//
//
//            $row = $this->repository->create($input);
//
//            $message = get_singular($this->resource).' '."created successfully.";
//
//            $data = $row;
//
//            return ApiResponseHelper::sendSuccessResponse($data, $message, 201);
//        } catch (ValidationException $e) {
//            return ApiResponseHelper::sendErrorResponse($e->errors(), "Failed to create".' '.get_singular($this->resource), 422);
//        } catch (\Exception $e) {
//            if (method_exists($e,'errors')){
//                return ApiResponseHelper::sendErrorResponse($e->errors(), "Failed to create".' '.get_singular($this->resource), 422);
//            }
//            else{
//                return ApiResponseHelper::sendErrorResponse($e->getMessage(), "Failed to create".' '. get_singular($this->resource), 500);
//            }
//        }
//    }
    public function update(Request $request, $id)
    {
        try {
            $where_clause = [
                'owner_id' => $id, // Replace with your conditions
            ];

            $input = $this->validation_on_creation($request,$id);

            $data = $this->repository->updateWhere($input, $where_clause);

            $message = "Clinic updated successfully.";

            return ApiResponseHelper::sendSuccessResponse($data, $message, 200);
        } catch (ValidationException $e) {
            return ApiResponseHelper::sendErrorResponse($e->errors(), "Failed to update Clinic", 500);
        } catch (\Exception $e) {
            if (method_exists($e,'errors')){
                return ApiResponseHelper::sendErrorResponse($e->errors(), "Failed to update Clinic", 422);
            }
            else{
                return ApiResponseHelper::sendErrorResponse($e->getMessage(), "Failed to update Clinic", 500);
            }
        }
    }

//    private function geocodeAddress($address)
//    {
//        // Use a geocoding service to get latitude and longitude
//        // This is an example using the Google Maps Geocoding API
//        $apiKey = env('GOOGLE_MAPS_API_KEY');
//        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&key=" . $apiKey;
//
//        $response = file_get_contents($url);
//        $json = json_decode($response, true);
//
//        if ($json['status'] === 'OK') {
//            return $json['results'][0]['geometry']['location'];
//        }
//
//        return null;
//    }
}
