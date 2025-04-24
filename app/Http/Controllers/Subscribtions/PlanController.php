<?php

namespace App\Http\Controllers\Subscribtions;

use App\Http\Controllers\Controller;
use App\Models\Subcriptions\Plan;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    public function createPlan(Request $request)
    {

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $plan = \Stripe\Price::create([
            "unit_amount" => $request->price * 100,     // ১০ ডলার হলে ১০ * ১০০ = ১০০০
            "currency" => 'usd',                        // মুদ্রা
            "recurring" => [
                "interval" => $request->interval,       // মাসিক বা বাৎসরিক: 'month' বা 'year'
                "trial_period_days" => $request->trial_period_days, // ফ্রি ট্রায়ালের দিন সংখ্যা
            ],
            "lookup_key" => Str::snake($request->name, '-'), // Stripe dashboard এ খোঁজার জন্য ইউনিক key
            "product_data" => [
                "name" => $request->name,               // প্রোডাক্টের নাম যেমন: 'Expert Plan'
            ],
        ]);

        $newPlan = new Plan();

        if ($plan && $plan->active === true) {
            $newPlan->name = $request->name;
            $newPlan->price = $request->price;
            $newPlan->interval = $request->interval;
            $newPlan->trial_period_days = $request->trial_period_days;
            $newPlan->stripe_plan_id = $plan->id;
            $newPlan->lookup_key = Str()->snake($request->name);
            $newPlan->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Plan created successfully',
            'data' => $newPlan
        ]);
    }

    public function getPlans()
    {
        $plans = Plan::all();
        return response()->json([
            'status' => true,
            'message' => 'All plans list',
            'data' => $plans
        ]);
    }
}
