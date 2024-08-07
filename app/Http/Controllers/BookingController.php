<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Repositories\BookingRepository;
use Illuminate\Http\Request;
use App\Helpers\ApiResponseHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BookingController extends BaseController
{
    protected $bookingRepository;

    public function __construct(BookingRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
        parent::__construct($bookingRepository, "Booking");
    }

    public function index()
    {
        try {
            $with = ['user','service'];

            $perPage = 10;

            $bookings = $this->bookingRepository->all($with,$perPage);

            $message = 'Bookings retrieved successfully.';

            return ApiResponseHelper::sendSuccessResponse($bookings, $message, 200);
        } catch (\Exception $e) {

            return ApiResponseHelper::sendErrorResponse($e->getMessage(), 'Failed to retrieve bookings.', 500);
        }
    }

    protected function validation_on_creation(Request $request,$id=false)
    {

        $validator = Validator::make($request->all(), [
            'service_id' => 'sometimes|required|exists:services,id',
            'created_by' => 'sometimes|required|exists:users,id',
            'booking_date' => 'sometimes|required|date',
            'booking_time' => 'sometimes|required|date_format:H:i:s',
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

    public function getBookings(Request $request)
    {
        $ownerId = $request->user()->id; // Assuming the owner is authenticated
        $date = $request->date;

        // Validate the input
        $request->validate([
            'date' => 'required|date',
        ]);

        $bookings = Booking::where('owner_id', $ownerId)
            ->where('booking_date', $date)
            ->get();

        // Fetch team member information
        $teamMembers = User::whereIn('id', $bookings->pluck('team_id')->unique())
            ->where('role', 'staff') // Adding the condition to check the status
            ->get()
            ->keyBy('id');

        $groupedBookings = $bookings->groupBy('booking_time')->map(function ($timeSlotBookings) use ($teamMembers) {
            return $timeSlotBookings->map(function ($booking) use ($teamMembers) {
                return [
                    'booking_id' => $booking->id,
                    'service_id' => $booking->service_id,
                    'service_name' => $booking->service->name, // Assuming service relation
                    'category_id' => $booking->category_id,
                    'status' => $booking->status,
                    'created_by' => $booking->created_by,
                    'updated_by' => $booking->updated_by,
                    'team_member' => $teamMembers->get($booking->team_id), // Fetch team member info from the pre-fetched list
                ];
            });
        });

        // Format the response
        $response = [
            'date' => $date,
            'team_members' => $teamMembers,
            'bookings' => $groupedBookings,
        ];

        $message = 'Bookings searched successfully.';

        return ApiResponseHelper::sendSuccessResponse($response, $message, 200);
    }

}

