<?php

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ClinicHoursController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {


    Route::middleware(['jwt.verify', 'is_admin:users'])->group(function () {
        foreach (get_user_slugs() as $page_slug => $pageController) {
            $controllerClass = "App\\Http\\Controllers\\{$pageController}";
            Route::prefix($page_slug)->group(function () use ($controllerClass) {
                Route::get('/', [$controllerClass, 'index']);
                Route::get('/show/{id}', [$controllerClass, 'show']);
                Route::post('/store', [$controllerClass, 'store']);
                Route::put('/update/{id}', [$controllerClass, 'update']);
                Route::delete('/delete/{id}', [$controllerClass, 'destroy']);
                Route::get('/search', [$controllerClass, 'search']);
            });
        }
    });

    Route::post('/add/hours',[ClinicHoursController::class, 'addHours']);
    Route::post('/get/bookings',[BookingController::class, 'getBookings']);
    Route::post('/rating', [RatingController::class, 'storeRating']);
    // Auth Routes

    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/resend', [AuthController::class, 'resend']);
    Route::post('/activation', [AuthController::class, 'activation']);
    Route::post('/login', [AuthController::class, 'login'])->middleware('is_verify');
    Route::post('/forget_password', [AuthController::class, 'forgetPassword']);
    Route::post('/verify_email', [AuthController::class, 'beforeVerifyEmail']);
    Route::post('/verify_code', [AuthController::class, 'verifyCode']);
    Route::post('/send-otp', [AuthController::class, 'beforeSendOtp'])->name('sendOtp');
    Route::post('/check-otp', [AuthController::class, 'checkOtp'])->name('checkOtp');

    Route::middleware(['jwt.verify'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::post('/me', [AuthController::class, 'getAuthenticatedUser']);
        Route::post('/generateQr', [AuthController::class, 'generateQrCode']);
        Route::post('/verifyQrCode', [AuthController::class, 'verifyQrCode']);
        Route::post('/update/password', [AuthController::class, 'updatePassword']);
        Route::post('/update/client/password', [AuthController::class, 'updateClientPassword']);
    });

    // SSO Routes

    Route::get('auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('auth/facebook', [AuthController::class, 'redirectToFacebook'])->name('login.facebook');
    Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
    Route::get('auth/facebook/callback', [AuthController::class, 'handleFacebookCallback']);

//    Route::post('/generateQr', [AuthController::class, 'generateQrCode']);
//    Route::post('/verifyQrCode', [AuthController::class, 'verifyQrCode']);

});


Route::any('{any}', function () {
    return ApiResponseHelper::sendErrorResponse([], 'Resource not found', 500);
})->where('any', '.*');
