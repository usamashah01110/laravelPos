<?php

namespace App\Http\Controllers\Unused;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\SubscriptionSuccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class PlanController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index(Request $request)
    {
        $plans = Plan::get();

        // Check if the request is an API request
        if ($request->is('api/*')) {

            return ApiResponseHelper::sendSuccessResponse($plans, 'Plans.', 200);

        } else {
            // If web request, render the 'plans' view with plans data
//            return view("plans", compact("plans"));
            return ApiResponseHelper::sendSuccessResponse($plans, 'Plans.', 200);
        }
    }

    public function getSubscriptions()
    {
        $user = Auth::user();

        // Check if the authenticated user is an admin
        if ($user->role === 'admin') {
            // If admin, retrieve all payments and subscriptions
            $subscriptions = Subscription::select('id', 'user_id', 'stripe_id', 'stripe_status', 'quantity', 'trial_ends_at', 'ends_at', 'created_at', 'updated_at')
                ->get();
        } else {
            // If user, retrieve only their own payments and subscriptions based on user_id
            $subscriptions = Subscription::where('user_id', $user->id)
                ->select('id', 'user_id', 'stripe_id', 'stripe_status', 'quantity', 'trial_ends_at', 'ends_at', 'created_at', 'updated_at')
                ->get();
        }

        return response()->json(['subscriptions' => $subscriptions]);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function show(Plan $plan, Request $request)
    {
        //Here i am logging in user by default but when there would be a real time scenario user will be loggied in and we can get it from the auth
        $user = User::find(1);
        $user = Auth::login($user);
        $user = Auth::user();
        $intent = $user->createSetupIntent();

        return view("subscription", compact("plan", "intent"));
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function subscription(Request $request)
    {
        $plan = Plan::find($request->plan);
        //Here i am logging in user by default but when there would be a real time scenario user will be loggied in and we can get it from the auth
        $user = User::find(1);
        $user = Auth::login($user);
        $user = Auth::user();

        $subscription =  $user->newSubscription($request->plan, $plan->stripe_plan)
                        ->create($request->token);

    // Data to be sent in the notification
    $subscriptionData = [
        'plan' => $plan->name,
        'amount' => $subscription->quantity * $plan->price, // Calculate the total amount based on quantity
    ];

    // Trigger the notification
    $user->notify(new SubscriptionSuccess($subscriptionData));
        return view("subscription_success");
    }




    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'slug' => 'required|string',
            'stripe_plan' => 'required|string|unique:plans',
            'price' => 'required|integer',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $plan = Plan::create($request->all());
        return response()->json($plan, 201);
    }

    public function view($id)
    {
        $plan = Plan::find($id);
        if (!$plan) {
            return response()->json(['error' => 'Plan not found'], 404);
        }
        return response()->json($plan);
    }

    public function update(Request $request, $id)
    {
        $plan = Plan::find($id);
        if (!$plan) {
            return response()->json(['error' => 'Plan not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'slug' => 'required|string',
            'stripe_plan' => 'required|string|unique:plans,stripe_plan,' . $plan->id,
            'price' => 'required|integer',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $plan->update($request->all());
        return response()->json($plan, 200);
    }

    public function destroy($id)
    {
        $plan = Plan::find($id);
        if (!$plan) {
            return response()->json(['error' => 'Plan not found'], 404);
        }
        $plan->delete();
        return response()->json(['message' => 'Plan deleted successfully']);
    }
}
