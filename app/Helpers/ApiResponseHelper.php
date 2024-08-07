<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ApiResponseHelper
{
    public static function sendSuccessResponse($data = null, $message = '', $status = 200)
    {
        $response = [
            'status' => true,
            'data' => $data,
            'msg' => $message,
            'errors'=>[]
        ];
        return response()->json($response);
    }

    public static function sendErrorResponse($errorData, $message, $status)
    {
        $response = [];
        $response['status'] = "Error";
        $response['msg'] = $message;
        $response['data'] = [];
        $response['errors'] = [];

        if (!empty($errorData)) {
            if (is_array($errorData)) {
                $newErrorDataArr = [];
                foreach ($errorData as $item => $value) {
                    $newErrorDataArr[$item] = $value[0];
                }
                $response['errors'] = $newErrorDataArr;
            } else {
                $response['errors'] = $errorData;
            }
        }

        return response()->json($response, $status);
    }
}
