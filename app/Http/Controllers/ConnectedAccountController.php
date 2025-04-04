<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Stripe;

class ConnectedAccountController extends Controller
{
    // public function createAccount(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'type'  => 'required|in:express,standard,custom',
    //         'email' => 'required|email',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['status' => false, 'message' => $validator->errors()], 400);
    //     }

    //     try {
    //         Stripe::setApiKey(env('STRIPE_SECRET'));

    //         $user = User::where('email', $request->email)->first();
    //         if (! $user) {
    //             return response()->json(['error' => 'User not found.'], 404);
    //         }

    //         // Stripe Connected Account তৈরি করা
    //         $account = Account::create([
    //             'type'         => $request->type,
    //             'email'        => $request->email,
    //             'capabilities' => [
    //                 'card_payments' => ['requested' => true],
    //                 'transfers'     => ['requested' => true],
    //             ],
    //         ]);


    //         // ইউজারের Stripe Account ID সেভ করা
    //         $user->update(['stripe_connect_id' => $account->id]);

    //         return response()->json([
    //             'status'  => true,
    //             'message' => 'Stripe Connect account created successfully.',
    //             'account_id' => $account->id,
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    public function createAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type'  => 'required|in:express,standard,custom',
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()], 400);
        }

        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $user = User::where('email', $request->email)->first();

            if (! $user) {
                return response()->json(['error' => 'User not found.'], 401);
            }

            // Create a connected account
            $account = Account::create([
                'type'         => 'express',
                'email'        => $request->email,
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers'     => ['requested' => true],
                ],
            ]);

            // Save the connected account ID
            $user->update(['stripe_connect_id' => $account->id]);

            // $url = url('/account-success') . '?' . http_build_query([
            //     'status' => 'success',
            //     'email' => $user->email,
            //     'account_id' => $account->id,
            // ]);
            $url = url("account-success?status=success&email={$user->email}&account_id={$account->id}");

            // Generate an onboarding link
            $accountLink = AccountLink::create([
                'account'     => $account->id,
                'refresh_url' => 'https://yourwebsite.com/reauth',
                'return_url'  => $url, // use the generated URL
                'type'        => 'account_onboarding',
            ]);

            return response()->json([
                'account_id'     => $account->id,
                'onboarding_url' => $accountLink->url,
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
