<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponseHelper;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function storeRating(Request $request)
    {
        try {
            $data = $request->validate([
                'user_id' => 'required|exists:users,id',
                'clinics_id' => 'required',
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'nullable|string',
            ]);
            $rating = Rating::create($data);
            $message = 'Rating created successfully.';
            return ApiResponseHelper::sendSuccessResponse($rating, $message, 200);
        } catch (\Exception $e) {
            return ApiResponseHelper::sendErrorResponse([], $e->getMessage(), 500);
        }
    }
}
