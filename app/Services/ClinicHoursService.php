<?php

namespace App\Services;

use App\Models\ClinicHours;
use Carbon\Carbon;

class ClinicHoursService
{
    public static function createDefaultClinicHours($clinicId)
    {
        $daysOfWeek = [
            'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'
        ];

        $defaultClinicHours = [];
        foreach ($daysOfWeek as $day) {
            $startTime = Carbon::now()->startOfWeek()->setTime(10, 0); // Start at 10 AM
            $endTime = $startTime->copy()->addHours(8); // Add 8 hours to the start time

            $defaultClinicHours[] = [
                'clinics_id' => $clinicId,
                'start_time' => $startTime->format('Y-m-d H:i:s'), // Full timestamp format
                'end_time' => $endTime->format('Y-m-d H:i:s'), // Full timestamp format
                'day_of_week' => $day,
                'is_open' => true,
                'created_by' => $clinicId,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Move start time to the next day for each iteration
            $startTime->addDay();
        }

        ClinicHours::insert($defaultClinicHours);
    }
}
