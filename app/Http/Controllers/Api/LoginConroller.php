<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;

class LoginConroller extends Controller
{
    /**
     * Login a user and return a token.
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user,
            ]);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }
    
    public function logout(Request $request)
    {
        $user = auth()->user();
        $user->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
