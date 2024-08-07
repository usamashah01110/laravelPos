<?php

namespace App\Http\Controllers\Unused;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class UserSettingController extends Controller
{
    public function index(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $userSetting = UserSetting::where('user_id', $user->id)->get();
            return response()->json([
                'message' => 'Get and show userSetting',
                'userSetting' => $userSetting,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'User Settings did not found', 'error' => $e->getMessage()], 500);
        }
    }

    public function create(Request $request)
    {
        $userSetting = UserSetting::firstOrNew(['user_id' => $request->user_id]);

        if ($request->twillio == 'twillio') {
            $validator = Validator::make($request->all(), [
                'twillio' => 'required',
                'twillio_client_id' => 'required',
                'twillio_secret_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $userSetting->twillio = $request->twillio;
            $userSetting->twillio_client_id = $request->twillio_client_id;
            $userSetting->twillio_secret_id = $request->twillio_secret_id;
        } elseif ($request->twoFactor == 'twoFactor') {
            $validator = Validator::make($request->all(), [
                'twoFactor' => 'required',
                'twoFactor_type' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $userSetting->twoFactor = $request->twoFactor;
            $userSetting->twoFactor_type = $request->twoFactor_type;
        } elseif ($request->payment_method_type == 'stripe') {
            $validator = Validator::make($request->all(), [
                'payment_method_type' => 'required',
                'payment_method_client_id' => 'required',
                'payment_method_secret_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $userSetting->payment_method_type = $request->payment_method_type;
            $userSetting->payment_method_secret_id = $request->payment_method_secret_id;
            $userSetting->payment_method_client_id = $request->payment_method_client_id;
        } else {
            return response()->json([
                'message' => 'Failed to save Setting Due to empty type',
                'error' => 'Failed to save Setting Due to empty type',
            ], 500);
        }

        $userSetting->save();

        return response()->json([
            'message' => 'userSetting has been saved',
            'userSetting' => $userSetting,
        ], 200);
    }


    public function update(Request $request, $id)
    {
        try {
            $userSetting = UserSetting::findOrFail($id);
            $userSetting->payment_method_type = $request->payment_method_type ? $request->payment_method_type : $userSetting->payment_method_type;
            $userSetting->payment_method_client_id = $request->payment_method_client_id ? $request->payment_method_client_id : $userSetting->payment_method_client_id;
            $userSetting->payment_method_secret_id = $request->payment_method_secret_id ? $request->payment_method_secret_id : $userSetting->payment_method_secret_id;
            $userSetting->twoFactor_type = $request->twoFactor_type ? $request->twoFactor_type : $userSetting->twoFactor_type;
            $userSetting->twoFactor = $request->twoFactor ? $request->twoFactor : $userSetting->twoFactor;
            $userSetting->twillio = $request->twillio ? $request->twillio : $userSetting->twillio;
            $userSetting->twillio_secret_id = $request->twillio_secret_id ? $request->twillio_secret_id : $userSetting->twillio_secret_id;
            $userSetting->twillio_client_id = $request->twillio_client_id ? $request->twillio_client_id : $userSetting->twillio_client_id;
            $userSetting->update();
            return response()->json([
                'message' => 'Update userSetting',
                'userSetting' => $userSetting,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'User Settings did not found', 'error' => $e->getMessage()], 500);
        }
    }
}
