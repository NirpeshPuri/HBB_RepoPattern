<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repository\interfaces\ProfileUpdateRepositoryInterface;

class ProfileUpdateController extends Controller
{
    protected $profileRepo;

    public function __construct(ProfileUpdateRepositoryInterface $profileRepo)
    {
        $this->profileRepo = $profileRepo;
    }

    // GET /api/profile
    public function show()
    {
        $data = $this->profileRepo->getProfile();
        return response()->json($data, 200);
    }

    // PUT /api/profile
    public function update(Request $request)
    {
        $result = $this->profileRepo->updateProfile($request);
        return response()->json($result, $result['success'] ? 200 : 422);
    }
}
