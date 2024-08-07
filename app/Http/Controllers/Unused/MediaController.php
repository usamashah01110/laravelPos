<?php

namespace App\Http\Controllers\Unused;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MediaController extends Controller
{


    /**
     * @OA\Post(
     *     path="/media",
     *     summary="Upload a media file",
     *     tags={"Media"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="title",
     *                     type="string",
     *                     description="Title of the media",
     *                     example="Sample Media"
     *                 ),
     *                 @OA\Property(
     *                     property="file",
     *                     type="string",
     *                     format="binary",
     *                     description="Media file to upload (Supported formats: jpeg, png, jpg, gif, svg, pdf, docx)"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Media uploaded successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Your Media file has been uploaded successfully."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=208,
     *         description="File with the same title already exists",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="errors",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         example="The same title name File already exists."
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 description="Validation errors",
     *             )
     *         )
     *     )
     * )
     */
    public function mediaUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg,pdf,docx',
        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::sendErrorResponse($validator->errors(), 'Validation Error', 422);
        }

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();

        // Check if a file with the same name exists
        $checkFile = Media::where('title', $request->title)->first();
//        dd($checkFile);
        if (!$checkFile) {
            // File does not exist in the database, proceed with upload
            $file->storeAs('uploads', $fileName);

            Media::updateOrCreate(
                ['title' => $request->title],
                [
                    'title' => $request->title,
                    'filename' => $fileName
                ]
            );

            $response = ['message' => 'Your Media file has been uploaded successfully.'];
            return ApiResponseHelper::sendSuccessResponse($response, 'Media has been uploaded successfully.', 200);
        } else {
            // File with the same name already exists in the database
            return ApiResponseHelper::sendErrorResponse([], ['message' => 'The Media title already exists. Please change it.'], 208);
        }
    }

    /**
     * @OA\Get(
     *     path="/get-media/{id}",
     *     summary="Get a specific media by ID",
     *     tags={"Media"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the media",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Successfully loaded."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Media not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="errors",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         example="Media not found."
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function getMedia($id)
    {
        $media = Media::find($id);
        if ($media) {
            return ApiResponseHelper::sendSuccessResponse($media, "successfully loaded.", 200);
        } else {
            return ApiResponseHelper::sendErrorResponse([], ['message' => 'Media not found.'], 404);
        }
    }


    /**
     * @OA\Get(
     *     path="/all-media",
     *     summary="Get all media",
     *     tags={"Media"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with media data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="All media successfully loaded."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Media not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="errors",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         example="Media not found."
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    public function getAllMedia()
    {
        $media = Media::all();
        if ($media->isNotEmpty()) {
            $data = $media;
            return ApiResponseHelper::sendSuccessResponse($data, 'All media successfully loaded.', 200);
        } else {
            return ApiResponseHelper::sendErrorResponse([], 'Media not found.', 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/update-media/{id}",
     *     summary="Update media by ID",
     *     tags={"Media"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the media to update",
     *          @OA\Schema(type="integer", format="int64")
     *      ),
     *    @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="title",
     *                      type="string",
     *                      description="Title of the media",
     *                      example="Sample Media"
     *                  ),
     *                  @OA\Property(
     *                      property="file",
     *                      type="string",
     *                      format="binary",
     *                      description="Media file to upload (Supported formats: jpeg, png, jpg, gif, svg, pdf, docx)"
     *                  )
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Media successfully updated."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Media not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="errors",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         example="Record not found."
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="title",
     *                     type="array",
     *                     @OA\Items(type="string")
     *                 ),
     *                 @OA\Property(
     *                     property="file",
     *                     type="array",
     *                     @OA\Items(type="string")
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    public function updateMedia(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);
        if ($validator->fails()) {
            return ApiResponseHelper::sendErrorResponse($validator->errors(), 'Validation Error', 422);
        }
        $checkFile = Media::where('id', $id)->first();
//        $dbfilename = $checkFile->filename;
//        dd(isset($checkFile));
        if (isset($checkFile)) {
            $file = $request->file('file');
            $fileName = $request->file('file')->getClientOriginalName();
            $file->storeAs('uploads', $fileName);
            Media::updateOrCreate([
                'id' => $id
            ], [
                'title' => $request->title,
                'filename' => $fileName
            ]);
            $response = ['message' => 'Media successfully updated.'];
            return ApiResponseHelper::sendSuccessResponse($response, 'Media successfully updated.', 200);
        } else {
            return ApiResponseHelper::sendErrorResponse([], 'Record not found.', 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/delete-media/{id}",
     *     summary="Delete media by ID",
     *     tags={"Media"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the media to delete",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Media successfully deleted."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Media not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="errors",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         example="Record not found."
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    public function deleteMedia($id)
    {
        $data = Media::find($id);
        if (isset($data)) {
            $data->delete();
            $response = ['message' => 'Media successfully deleted.'];
            return ApiResponseHelper::sendSuccessResponse($response, "Media successfully deleted.", 200);
        } else {
            return ApiResponseHelper::sendErrorResponse([], 'Record not found.', 404);
        }
    }

}
