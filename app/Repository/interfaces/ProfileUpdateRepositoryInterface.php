<?php

namespace App\Repository\interfaces;

use Illuminate\Http\Request;

interface ProfileUpdateRepositoryInterface
{
    public function getProfile();
    public function updateProfile(Request $request);
}
