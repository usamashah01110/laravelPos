<?php

namespace App\Http\Middleware;


use App\Helpers\ApiResponseHelper;
use Closure;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;


class JwtMiddleware extends BaseMiddleware
{
    public function sendError($errorData, $message, $status)
    {
        $response = [];
        $response['message'] = $message;
//        $response['status'] = $status;
        if (!empty($errorData)) {
            $errorDataArray = json_decode($errorData);
            $newErrorDataArr = [];
            foreach ($errorDataArray as $item => $value) {
                $newErrorDataArr[$item] = $value[0];
            }

            $response['errors'] = $newErrorDataArr;
        }
        return response()->json($response, $status);
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){

                return ApiResponseHelper::sendErrorResponse([], 'Token is Invalid', 403);

            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return ApiResponseHelper::sendErrorResponse([], 'Token is Expired', 401);

            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenBlacklistedException){
                return ApiResponseHelper::sendErrorResponse([], 'Token is Blacklisted', 400);
            }else{
                return ApiResponseHelper::sendErrorResponse([], 'Authorization Token not found', 404);
            }
        }
        return $next($request);
    }
}
