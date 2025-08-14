<?php

namespace App\Repository;

use App\Repository\interfaces\LoginRepositoryInterface;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginRepository implements LoginRepositoryInterface
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'user_type' => 'required|in:receiver,donor,admin',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        if ($user->user_type !== $request->user_type) {
            return ['success' => false, 'message' => 'User type mismatch'];
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'success' => true,
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ];
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->tokens()->delete();
            return ['success' => true, 'message' => 'Logged out successfully'];
        }

        return ['success' => false, 'message' => 'No authenticated user'];
    }
}
