<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:5'
        ]);

        if ($validated->fails()) {
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $validated->errors()
            ], 422);
        }


        $user = User::select(['id', 'name', 'email', 'password'])->where('email', $request->email)->first();

        if ($user) {

            if ($request->password == $user->password) {

                $user['accessToken'] = $user->createToken('API TOKEN')->plainTextToken;

                // if success
                return response()->json([
                    'message' => 'Login success',
                    'user' => $user
                ], 200);
            }
        }

        return response()->json([
            "message" => "Email or password incorrect"
        ], 401);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

        return response()->json([
            "message" => "Logout success"
        ], 200);
    }
}
