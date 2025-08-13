<?php

namespace App\Repositories\interfaces;

use Illuminate\Http\Request;

interface ProfileUpdateRepositoryInterface
{
    /**
     * Get authenticated user profile along with blood types.
     *
     * @return array
     */
    public function getProfile();

    /**
     * Update the authenticated user's profile.
     *
     * @param Request $request
     * @return array
     */
    public function updateProfile(Request $request);
}
