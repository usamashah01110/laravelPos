<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponseHelper;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use Nette\Schema\ValidationException;

abstract class BaseController extends Controller
{
    protected $repository;
    protected $resource;

    public function __construct(BaseRepositoryInterface $repository, $resource)
    {
        $this->repository = $repository;
        $this->resource = $resource;
    }

    abstract protected function _pre_save($data);
    abstract protected function validation_on_creation(Request $request,$id=false);
    abstract protected function _post_save($data);

    public function index()
    {
        try {
            $data = $this->repository->all();
            $message = get_plural($this->resource)."retrieved successfully";
            return ApiResponseHelper::sendSuccessResponse($data, $message, 200);
        } catch (\Exception $e) {
            $response = [
                'message' => 'Failed to retrieve'.get_plural($this->resource),
                'error' => $e->getMessage()
            ];
            return ApiResponseHelper::sendErrorResponse([], $response, 500);
        }
    }

    public function show($id)
    {
        $data = $this->repository->get($id);
        if ($data) {
            return response()->json([
                'status' => true,
                'data' => $data,
                'msg' => get_plural($this->resource). 'retrieved successfully',
                'errors' => null
            ]);
        }
        return response()->json([
            'status' => false,
            'data' => null,
            'msg' => get_plural($this->resource).'Data Not Found',
            'errors' => ["Data not found"]
        ], 404);
    }

    public function store(Request $request)
    {
        try {
            $input = $this->validation_on_creation($request);

            if ($request->hasFile('image_urls')) {
                $input['image_urls'] = $this->_pre_save($request->file('image_urls'));
            }

            $row = $this->repository->create($input);

            $message = get_singular($this->resource).' '."created successfully.";

            $data = $row;

            return ApiResponseHelper::sendSuccessResponse($data, $message, 201);
        } catch (ValidationException $e) {
            return ApiResponseHelper::sendErrorResponse($e->errors(), "Failed to create".' '.get_singular($this->resource), 422);
        } catch (\Exception $e) {
            if (method_exists($e,'errors')){
                return ApiResponseHelper::sendErrorResponse($e->errors(), "Failed to create".' '.get_singular($this->resource), 422);
            }
            else{
                return ApiResponseHelper::sendErrorResponse($e->getMessage(), "Failed to create".' '. get_singular($this->resource), 500);
            }
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $input = $this->validation_on_creation($request,$id);
            $data = $this->repository->updateWhere($input, $id);
            $message = get_singular($this->resource). " updated successfully.";
            return ApiResponseHelper::sendSuccessResponse($data, $message, 200);
        } catch (ValidationException $e) {
            return ApiResponseHelper::sendErrorResponse($e->errors(), "Failed to update". get_singular($this->resource), 500);
        } catch (\Exception $e) {
            if (method_exists($e,'errors')){
                return ApiResponseHelper::sendErrorResponse($e->errors(), "Failed to update".get_singular($this->resource), 422);
            }
            else{
                return ApiResponseHelper::sendErrorResponse($e->getMessage(), "Failed to update".get_singular($this->resource), 500);
            }
        }
    }

    public function destroy(Request $request,$id)
    {
        try {
            $this->repository->delete($id);
            $message = get_singular($this->resource). " deleted successfully.";
            $data = null;
            return ApiResponseHelper::sendSuccessResponse($data, $message, 200);
        } catch (\Exception $e) {
            $response = [
                'message' => "Failed to Delete ".get_singular($this->resource),
                'error' => $e->getMessage()
            ];
            return ApiResponseHelper::sendErrorResponse([], $response, 500);
        }
    }


    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $radius = $request->input('radius', 10);
        try {
            $data = $this->repository->search($keyword,$latitude,$longitude,$radius);

            if(!empty($data)){
                $message = get_plural($this->resource) . " no match found ";
            }else{
                $message = get_plural($this->resource) . " found successfully";
            }
            return ApiResponseHelper::sendSuccessResponse($data, $message, 200);
        } catch (\Exception $e) {
            return ApiResponseHelper::sendErrorResponse($e->getMessage(), get_plural($this->resource), 500);
        }
    }



}
