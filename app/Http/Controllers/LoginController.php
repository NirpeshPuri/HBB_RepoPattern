<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repository\interfaces\LoginRepositoryInterface;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __construct(protected LoginRepositoryInterface $loginRepo)
    {
        $this->loginRepo = $loginRepo;
    }

    public function login(Request $request)
    {
        return response()->json($this->loginRepo->login($request));
    }

    public function logout(Request $request)
    {
        return response()->json($this->loginRepo->logout($request));
    }
}
