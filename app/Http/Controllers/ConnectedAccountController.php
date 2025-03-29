<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\Account;
use Stripe\Stripe;

class ConnectedAccountController extends Controller
{
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
                return response()->json(['error' => 'User not found.'], 404);
            }

            // Stripe Connected Account তৈরি করা
            $account = Account::create([
                'type'         => $request->type,
                'email'        => $request->email,
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers'     => ['requested' => true],
                ],
            ]);

            // ইউজারের Stripe Account ID সেভ করা
            $user->update(['stripe_connect_id' => $account->id]);

            return response()->json([
                'status'  => true,
                'message' => 'Stripe Connect account created successfully.',
                'account_id' => $account->id,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
