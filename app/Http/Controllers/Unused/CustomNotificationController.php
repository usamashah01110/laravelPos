<?php
namespace App\Http\Controllers\Unused;
use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class CustomNotificationController extends Controller
{
    //
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // If the user is an admin, retrieve all notifications
            $notifications = Notification::all();
        } else {
            // If the user is not an admin, retrieve only their own notifications
            $notifications = $user->notifications;
            // $user->unreadNotifications->markAsRead(); // Mark user's notifications as read
        }
        return ApiResponseHelper::sendSuccessResponse($notifications, 'All media successfully loaded.', 200);

    }
}
