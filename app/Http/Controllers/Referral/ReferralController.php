<?php

namespace App\Http\Controllers\Referral;

use App\Http\Controllers\Controller;
use App\Models\Network;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ReferralController extends Controller
{
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:3',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()], 400);
        }

        if ($request->has('ref')) {
            $parent = User::where('referral_code', $request->ref)->first();
            if ($parent) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                    'referral_code' => strtoupper(Str::random(8)),
                ]);

                Network::create([
                    'user_id' => $user->id,
                    'parent_id' => $parent->id,
                    'referral_code' => $request->ref
                ]);
                return response()->json([
                    'status' => true,
                    'message' => 'User register successfully created with referral_code.',
                    'data' => $user
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Referral code is invalied',
                ]);
            }
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'referral_code' => strtoupper(Str::random(8)),
            ]);
            return response()->json([
                'status' => true,
                'message' => 'User register successfully created',
                'data' => $user
            ]);
        }
    }

    public function parentWithAllClient($id)
    {
        $parent = User::find($id);
        $parentClient = Network::where('parent_id', $id)->get();
        return response()->json([
            'status' => true,
            'message' => 'Parent with all client',
            'parent' => $parent,
            'client' => $parentClient
        ]);
    }
}
