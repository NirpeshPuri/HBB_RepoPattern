<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface DonorRepositoryInterface
{
    public function showDonationPage();
    public function checkEligibility();
    public function findNearbyAdmins(Request $request);
    public function submitDonation(Request $request);
    public function getDonations();
    public function getDonationById($id);
    public function updateDonation(Request $request, $id);
    public function deleteDonation($id);
}
