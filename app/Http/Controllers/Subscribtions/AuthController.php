<?php

namespace App\Http\Controllers\Subscribtions;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:3'
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validation->errors()
                ], 422);
            }


            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'status' => false,
                    'message' => 'unauthorized'
                ], 401);
            }


            $user = User::where('email', $request->email)->first();


            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'login successfully',
                'token' => $token,
                'user' => $user
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 500);
        }
    }



    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->tokens()->delete();
        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully'
        ], 200);
    }
}
