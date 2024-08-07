<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Notifications\SinglePaymentSuccess;
use App\Models\Payment;


class PaymentController extends Controller
{
    //
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $payments = Payment::all();
        } else {
            $payments = Payment::where('user_id', $user->id)->get();
        }

        if ($payments->isEmpty()) {
            $response = ['error' => 'No payments found.'];
            return ApiResponseHelper::sendErrorResponse($response, [], 404);
        }

        return ApiResponseHelper::sendSuccessResponse($payments, 'payments', 200);
    }

    public function charge(Request $request, String $product, $price)
    {
        $user = User::find(1); // Find the user with ID 1
        $user = Auth::login($user);
        $user = Auth::user();

        return view('paymentTest', [
            'user' => $user,
            'intent' => $user->createSetupIntent(),
            'product' => $product,
            'price' => $price
        ]);
    }

    public function processPayment(Request $request, String $product, $price)
    {
        $user = User::find(1); // Find the user with ID 1
        $user = Auth::login($user);
        $user = Auth::user();
        $paymentMethod = $request->input('payment_method');
        $user->createOrGetStripeCustomer();
        $user->addPaymentMethod($paymentMethod);
        try {
            $user->charge($price * 100, $paymentMethod);
            //return response()->json([
            //    'message' => 'transaction succesfful'
            //]);
            $payment = new Payment();
            $payment->user_id = $user->id;
            $payment->product = $product;
            $payment->amount = $price;
            $payment->payment_method = $paymentMethod;
            $payment->status = 'Successful';
            $payment->save();
            $response = 'Transaction Successful';
            $paymentData['product'] =  $payment->product;
            $paymentData['price'] =  $payment->amount;
            $user->notify(new SinglePaymentSuccess($paymentData));
            return ApiResponseHelper::sendSuccessResponse($response, "Transaction Successful", 200);
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Error creating subscription. ' . $e->getMessage()]);
        }
        return redirect('home');
    }
}
