<?php

namespace App\Http\Middleware;

use App\Models\UserCode;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class VerifyEmailCode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
//        $validator = Validator::make($request->all(), [
//            'email' => 'required|email|string',
//            'code' => 'required|string|max:6',
//        ]);
//        if ($validator->fails()){
//            return \response()->json($validator->errors());
//        }
//        try {
//            $codeTB = UserCode::where('email',$request->email)->first();
//            $code = $codeTB->code;
//                if (auth()->check()  ){
//                    if ($code == $request->code){
//                        $codeExpiry = $codeTB->expired_at;
//                        if ($codeExpiry > now()){
//                            return $next($request);
//                        }else{
//                            return \response()->json('your code has been expired');
//                        }
//                    }else{
//                        return \response()->json('your code is Invalid');
//                    }
//                }
//                else{
//                return \response()->json('You are not authorized');
//            }
//        }catch(\Exception $e){
//            return \response()->json(['message'=>$e->getMessage()]);
//        }
        return $next($request);
    }
}
