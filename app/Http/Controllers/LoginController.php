<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\interfaces\LoginRepositoryInterface;

class LoginController extends Controller
{
    protected $loginRepo;

    public function __construct(LoginRepositoryInterface $loginRepo)
    {
        $this->loginRepo = $loginRepo;
    }

    // POST /api/login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'user_type' => 'required|in:admin,receiver,donor'
        ]);

        $result = $this->loginRepo->login($request->only('email', 'password', 'user_type'));

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => $result['user'],
                    'token' => $result['token'] ?? null
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 401);
        }
    }

    // POST /api/logout
    public function logout(Request $request)
    {
        $this->loginRepo->logout($request);

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }
}
