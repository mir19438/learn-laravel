<?php


namespace App\Http\Controllers;

use App\Models\Payment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Stripe\Product;
use Stripe\PaymentIntent;
use Stripe\PaymentLink;
use Stripe\Price;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;



class PaymentController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount'     => 'required',
            // 'payment_method' => 'required|string',
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
                // 'automatic_payment_methods' => ['enabled' => true],
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
        // input validation
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
            // Stripe API Key set
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            // Payment Intent
            $paymentIntent = \Stripe\PaymentIntent::retrieve($request->payment_intent_id);



            if ($paymentIntent->status == 'requires_confirmation') {
                // Success save to database
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


    // public function generatePaymentLink(Request $request)
    // {
    //     // $validator = Validator::make($request->all(), [
    //     //     'service_id' => 'required|numeric',
    //     //     'price'      => 'required|numeric',
    //     // ]);
    //     // if ($validator->fails()) {
    //     //     return response()->json(['status' => false, 'message' => $validator->errors()], 400);
    //     // }

    //     // $price                 = $request->price;
    //     // $totalAmount           = (int) ($price * 100);
    //     // $platformFeePercentage = 3; // 3% platform fee
    //     // $platformFee           = (int) (($totalAmount * $platformFeePercentage) / 100);

    //     // $service = SalonService::find($request->service_id);
    //     // if (! $service) {
    //     //     return response()->json(['status' => false, 'message' => 'Service not found.'], 404);
    //     // }

    //     // $salon = Salon::find($service->salon_id);
    //     // if (! $salon) {
    //     //     return response()->json(['status' => false, 'message' => 'Salon not found.'], 404);
    //     // }

    //     // $professional = User::find($salon->user_id);
    //     // if (! $professional || ! $professional->stripe_account_id) {
    //     //     return response()->json([
    //     //         'status'  => false,
    //     //         'message' => 'This professional does not have a Stripe account.',
    //     //     ], 400);
    //     // }

    //     Stripe::setApiKey(env('STRIPE_SECRET'));

    //     try {
    //         $session = Session::create([
    //             'payment_method_types' => ['card'],
    //             'line_items'           => [[
    //                 'price_data' => [
    //                     'currency'     => 'usd',
    //                     'product_data' => [
    //                         // 'name' => $service->service_name,
    //                         'name' => 'serviceName'
    //                     ],
    //                     // 'unit_amount'  => $totalAmount,
    //                     'unit_amount' => 10*100
    //                 ],
    //                 'quantity'   => 1,
    //             ]],
    //             'mode'                 => 'payment',
    //             'payment_intent_data'  => [
    //                 'application_fee_amount' => (int) ((10*100 * 3) / 100),
    //                 'transfer_data'          => [
    //                     'destination' => 'stripe_account_id',
    //                 ],
    //             ],
    //             // 'success_url'          => url('/payment-success?session_id={CHECKOUT_SESSION_ID}'),
    //             'success_url'          => url('/payment-success') . '?session_id={CHECKOUT_SESSION_ID}&user_id=' . 19438 .
    //             '&user_email=' . 'shifat@gmai.com' . '&salon_id=' . 'salon_id' .
    //             '&service_id=' . 'service_id' . '&price=' . 'price' .
    //             '&schedule_date=' . 'schedule_date' . '&schedule_time=' . 'schedule_time',

    //             'cancel_url'           => url('/payment-failed'),
    //         ]);
    //         if (isset($session->id) && $session->id != '') {
    //             return response()->json([
    //                 'status'       => true,
    //                 'payment_link' => $session->url,
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'status'       => false,
    //                 'payment_link' => null,
    //             ]);
    //         }
    //     } catch (Exception $e) {
    //         return response()->json(['status' => 'false ok', 'error' => $e->getMessage()], 500);
    //     }
    // }

    // public function createStripePaymentLink()
    // {
    //     Stripe::setApiKey(env('STRIPE_SECRET'));

    //     try {
    //         // 1. Create a product
    //         $product = \Stripe\Product::create([
    //             'name' => 'Haircut with Style',
    //         ]);

    //         // 2. Create a price for the product
    //         $price = \Stripe\Price::create([
    //             'unit_amount' => 2000, // $20.00
    //             'currency' => 'usd',
    //             'product' => $product->id,
    //         ]);

    //         // 3. Create the payment link
    //         $paymentLink = \Stripe\PaymentLink::create([
    //             'line_items' => [[
    //                 'price' => $price->id,
    //                 'quantity' => 1,
    //             ]],
    //         ]);

    //         return response()->json([
    //             'status' => true,
    //             'payment_link' => $paymentLink->url,
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'error' => $e->getMessage(),
    //         ]);
    //     }
    // }

    // public function createAdvancedPaymentLink()
    // {
    //     Stripe::setApiKey(env('STRIPE_SECRET'));

    //     try {
    //         // 1. Create Product
    //         $product = Product::create([
    //             'name' => 'Premium Haircut Package',
    //         ]);

    //         // 2. Create Price (one-time or recurring depending on subscription)
    //         $price = Price::create([
    //             'unit_amount' => 2500, // $25.00
    //             'currency' => 'usd',
    //             'product' => $product->id,

    //             // 🔄 Enable this for subscriptions
    //             // 'recurring' => ['interval' => 'month'], // for subscription mode
    //         ]);

    //         // 3. Create Payment Link with advanced options
    //         $paymentLink = PaymentLink::create([
    //             'line_items' => [[
    //                 'price' => $price->id,
    //                 'quantity' => 1,
    //             ]],
    //             // ✅ 1. Success & Cancel URL
    //             'after_completion' => [
    //                 'type' => 'redirect',
    //                 'redirect' => [
    //                     'url' => url('/payment-success'),
    //                 ],
    //             ],
    //             // 'cancel_url' => url('/payment-cancel'),

    //             // ✅ 2. Allow promotion codes
    //             'allow_promotion_codes' => true,

    //             // ✅ 3. Collect customer billing address
    //             'billing_address_collection' => 'required',

    //             // ✅ 4. Subscription mode (optional, see price creation above)
    //             // 'mode' => 'subscription', // Optional, for recurring payments
    //         ]);

    //         return response()->json([
    //             'status' => true,
    //             'payment_link' => $paymentLink->url,
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'error' => $e->getMessage(),
    //         ]);
    //     }
    // }

    public function createCheckoutSession(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Premium Haircut Package',
                        ],
                        'unit_amount' => 2500, // $25.00
                    ],
                    'quantity' => 1,
                ]],
                // ✅ Subscription Mode
                // 'mode' => 'subscription',
                'mode' => 'payment',

                // ✅ Success & Cancel URL
                // http://127.0.0.1:8000/payment-success?session_id=cs_test_b1svPsm4aZpN9lwiqkyviM6G8bQtLwJPF5mI1sjzZKzI8YpzUjTt3B7pBU
                'success_url' => url('/payment-success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => url('/payment-cancel'),

                // ✅ Promotion codes
                'allow_promotion_codes' => true,

                // ✅ Billing Address
                'billing_address_collection' => 'required',
            ]);

            return response()->json([
                'status' => true,
                'checkout_url' => $session->url,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
