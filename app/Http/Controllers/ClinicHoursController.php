<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponseHelper;
use App\Models\ClinicHours;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClinicHoursController extends Controller
{

    public static function addHours(Request $request)
    {
        $clinicHours = $request->all();

        // Define validation rules
        $rules = [
            '*.clinics_id' => 'required|integer|exists:clinics,id',
            '*.start_time' => 'required|date_format:Y-m-d\TH:i:s',
            '*.end_time' => 'required|date_format:Y-m-d\TH:i:s|after:start_time',
            '*.day_of_week' => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            '*.is_open' => 'required|boolean',
            '*.created_by' => 'required|integer|exists:users,id'
        ];

        $validator = Validator::make($clinicHours, $rules);

        if ($validator->fails()) {
            return ApiResponseHelper::sendErrorResponse($validator->errors(), 'Validation failed', 422);
        }

        foreach ($clinicHours as $clinicHour) {
            $existingClinicHour = ClinicHours::where('clinics_id', $clinicHour['clinics_id'])
                ->where('day_of_week', $clinicHour['day_of_week'])
                ->first();

            if ($existingClinicHour) {
                // Update the existing record
                $existingClinicHour->update($clinicHour);
            } else {
                // Create a new record
                ClinicHours::create($clinicHour);
            }
        }

        $message = 'Clinic hours successfully processed.';
        return ApiResponseHelper::sendSuccessResponse([], $message, 200);
    }

}
