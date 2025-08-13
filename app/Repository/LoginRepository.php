<?php

namespace App\Repository;

use App\Repository\interfaces\LoginRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginRepository implements LoginRepositoryInterface
{
    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'user_type' => 'required|in:receiver,donor,admin',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        // Admin login
        if ($request->user_type === 'admin') {
            if (Auth::guard('admin')->attempt($credentials, $remember)) {
                return ['success' => true, 'redirect' => route('admin.dashboard')];
            }
        } else { // Receiver or Donor login
            if (Auth::guard('web')->attempt($credentials, $remember)) {
                $user = Auth::guard('web')->user();
                if ($user->user_type === $request->user_type) {
                    return ['success' => true, 'redirect' => route($request->user_type . '.dashboard')];
                }
                Auth::guard('web')->logout(); // Log out if type mismatch
            }
        }

        return ['success' => false, 'message' => 'Invalid credentials or user type'];
    }

    public function logout(Request $request)
    {
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        } elseif (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return ['success' => true, 'message' => 'Logged out successfully'];
    }
}
