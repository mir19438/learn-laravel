<?php

namespace App\Http\Controllers\Subscribtions;

use App\Http\Controllers\Controller;
use App\Models\Subcriptions\Order;
use App\Models\Subcriptions\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Checkout\Session;
use Stripe\Invoice;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Subscription;

class SubPayment extends Controller
{
    public function checkout(Request $request, $id)
    {
        $user = Auth::user();
        $plan = Plan::where('id', $id)->first();

        if (!$plan) {
            return response()->json([
                'status' => false,
                'message' => 'Plan not found'
            ], 404);
        }

        // return $plan->stripe_plan_id;


        Stripe::setApiKey(env('STRIPE_SECRET'));

        $lineItems = [
            [
                'price' => $plan->stripe_plan_id,
                'quantity' => 1,
            ],
        ];

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'customer_email' => $user->email,
                'line_items' => $lineItems,
                // 'subscription_data' => [
                //     'trial_from_plan' => true,
                // ],
                'mode' => 'subscription',
                'success_url' => route('checkout-success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('checkout-cancel'),
            ]);



            $order = new Order();
            $order->user_id = $user->id;
            $order->status = 'unpaid';
            $order->total_price = $plan->price;
            $order->session_id = $session->id;
            $order->save();

            return response()->json([
                'status' => true,
                'message' => 'Stripe session link',
                'url' => $session->url,
                'payment intent id ' => $session->payment_intent
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }




    public function success(Request $request)
    {
        // $stripe = new StripeClient(env('STRIPE_SECRET'));
        // $session_id = $request->get('session_id');
        // $order = Order::where('session_id', $session_id)->first();
        // $session = $stripe->checkout->sessions->retrieve($session_id);

        // if (!$session) {
        //     return response()->json([
        //         'message' => 'Session not found'
        //     ]);
        // }

        // if (!$order) {
        //     return response()->json([
        //         'message' => 'Order not found'
        //     ]);
        // }

        // $order->status = 'paid';
        // $order->save();

        Stripe::setApiKey(env('STRIPE_SECRET'));
        // Step 1: Checkout session retrieve
        $session = Session::retrieve($request->get('session_id'));

        // Step 2: Get the subscription ID from session
        $subscriptionId = $session->subscription;

        // Step 3: Get subscription details
        $subscription = Subscription::retrieve($subscriptionId);

        // Step 4: Get latest invoice from subscription
        $invoice = Invoice::retrieve($subscription->latest_invoice);

        // Step 5: Get payment intent
        $paymentIntentId = $invoice->payment_intent;

        return response()->json([
            'status' => true,
            'subscription_id' => $subscriptionId,
            'invoice_id' => $invoice->id,
            'payment_intent_id' => $paymentIntentId,
        ]);


        // return response()->json([
        //     'status' => true,
        //     'message' => 'Order Created Successfully',
        //     'variable' => [
        //         'session_id' => $session_id,
        //         'order' => $order,
        //         'session' => $session
        //     ]
        // ]);
    }




    public function cancel()
    {
        return 'cancel';
    }

    public function getSubscription(Request $request, $id)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $subscription = Subscription::retrieve($id);

            return response()->json([
                'status' => true,
                'subscription' => $subscription
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getInvoice(Request $request, $id)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $invoice = Invoice::retrieve($id);

            return response()->json([
                'status' => true,
                'invoice' => $invoice
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getSubscriptionOfCustomer(Request $request, $id)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $subscriptions = Subscription::all([
                'customer' => $id,
                'limit' => 10,
            ]);

            return response()->json([
                'status' => true,
                'invoice' => $subscriptions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function cancelSubscriptionNow($id)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $subscription = Subscription::retrieve($id);
            $subscription->cancel();

            return response()->json([
                'status' => true,
                'message' => 'Subscription cancelled immediately.',
                'data' => $subscription,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
