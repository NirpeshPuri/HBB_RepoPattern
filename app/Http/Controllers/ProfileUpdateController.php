<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\ProfileUpdateRepositoryInterface;

class ProfileUpdateController extends Controller
{
    protected $profileRepo;

    public function __construct(ProfileUpdateRepositoryInterface $profileRepo)
    {
        $this->profileRepo = $profileRepo;
    }

    /**
     * GET /api/profile
     * Return authenticated user profile and blood types
     */
    public function show()
    {
        $data = $this->profileRepo->getProfile();
        return response()->json($data, 200);
    }

    /**
     * PUT /api/profile
     * Update authenticated user profile
     */
    public function update(Request $request)
    {
        $result = $this->profileRepo->updateProfile($request);
        return response()->json($result, 200);
    }
}
