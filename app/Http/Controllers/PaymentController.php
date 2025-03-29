<?php


namespace App\Http\Controllers;

use App\Models\Payment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Stripe\PaymentIntent;
use Stripe\Stripe;



class PaymentController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount'     => 'required',
            'payment_method' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()], 422);
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // $product = Product::find($request->product_id);
            // if (! $product) {
            //     return response()->json(['status' => false, 'message' => 'Product not found.'], 404);
            // }

            $amount = $request->amount * 100; // Convert to cents

            $paymentIntent = PaymentIntent::create([
                'amount'         => $amount,
                'currency'       => 'usd',
                'payment_method' => $request->payment_method,
                // 'confirmation_method' => 'manual',
                'confirm'        => false,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Payment intent created successfully.',
                'data'    => $paymentIntent,
            ], 200);
        } catch (Exception $e) {
            Log::error('Stripe Payment Intent Error: ' . $e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'Payment intent creation failed.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    public function paymentSuccess(Request $request)
    {
        // ইনপুট ভ্যালিডেশন
        $validator = Validator::make($request->all(), [
            'payment_intent_id' => 'required|string',
            'amount'            => 'required|numeric',
            'user_id'           => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()
            ], 400);
        }

        try {
            // Stripe API Key সেট করা
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            // Payment Intent ফেচ করা
            $paymentIntent = \Stripe\PaymentIntent::retrieve($request->payment_intent_id);



            if ($paymentIntent->status == 'requires_confirmation') {
                // Success হলে ডাটাবেজে সেভ করুন
                $payment = new Payment();
                $payment->user_id = $request->user_id;
                $payment->payment_intent_id = $request->payment_intent_id;
                $payment->amount = $request->amount;
                $payment->status = 'success';
                $payment->save();

                return response()->json([
                    'status'  => true,
                    'message' => 'Payment successful!',
                    'data'    => $payment
                ], 200);
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Payment not successful yet.',
                    'stripe_status' => $paymentIntent->status
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong!',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
