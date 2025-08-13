<?php

namespace App\Repository\interfaces;

use Illuminate\Http\Request;

interface LoginRepositoryInterface
{
    public function login(Request $request);
    public function logout(Request $request);
}
