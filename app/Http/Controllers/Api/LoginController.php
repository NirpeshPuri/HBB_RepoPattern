<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'     => 'required|email',
            'password'  => 'required|string',
            'user_type' => 'required|in:receiver,donor,admin',
        ]);

        $credentials = $request->only('email', 'password');

        if ($request->user_type === 'admin') {
            if (!Auth::guard('admin')->attempt($credentials)) {
                return response()->json(['message' => 'Invalid admin credentials'], 401);
            }
            $user = Auth::guard('admin')->user();
        } else {
            if (!Auth::guard('web')->attempt($credentials)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }
            $user = Auth::guard('web')->user();

            if ($user->user_type !== $request->user_type) {
                Auth::guard('web')->logout();
                return response()->json(['message' => 'User type mismatch'], 403);
            }
        }

        return response()->json([
            'message' => 'Login successful',
            'user'    => $user
        ], 200);
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        Auth::guard('admin')->logout();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
