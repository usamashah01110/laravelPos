<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Unused\ContactController;
use App\Http\Controllers\Unused\PlanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/login', function () {
    return view('login');
});


Route::get('/contact',[ContactController::class,'contactView']);// ok
Route::post('/contact',[ContactController::class,'contactSubmit']);// ok
Route::get('/contact/list',[ContactController::class,'contactsList']);// ok
Route::get('/contact/edit/{id}',[ContactController::class,'contactEdit']);// ok
Route::post('/contact/updated/{id}',[ContactController::class,'submitUpdateContact']);
Route::get('/contact-delete/{id}',[ContactController::class,'contactDelete']);
Route::get('/export/contact',[ContactController::class,'export']);
Route::post('/import/contact',[ContactController::class,'import']);
Route::get('/reset-password', [AuthController::class, 'callBackResetPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::get('/payment/{string}/{price}', [PaymentController::class, 'charge'])->name('goToPayment');
Route::get('plans', [PlanController::class, 'index']);
Route::get('plans/{plan}', [PlanController::class, 'show'])->name("plans.show");
Route::post('subscription', [PlanController::class, 'subscription'])->name("subscription.create");
Route::get('/payment-testing', function () { return view('paymentHome');});
Route::post('payment/process-payment/{string}/{price}', [PaymentController::class, 'processPayment'])->name('processPayment');
