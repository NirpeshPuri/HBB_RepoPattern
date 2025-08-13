<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\DonorRepositoryInterface;

class DonorController extends Controller
{
    protected $donorRepo;

    public function __construct(DonorRepositoryInterface $donorRepo)
    {
        $this->donorRepo = $donorRepo;
    }

    public function index()
    {
        return response()->json($this->donorRepo->getDonations());
    }

    public function show($id)
    {
        return response()->json($this->donorRepo->getDonationById($id));
    }

    public function store(Request $request)
    {
        return response()->json($this->donorRepo->submitDonation($request));
    }

    public function update(Request $request, $id)
    {
        return response()->json($this->donorRepo->updateDonation($request, $id));
    }

    public function destroy($id)
    {
        return response()->json($this->donorRepo->deleteDonation($id));
    }

    public function eligibility()
    {
        return response()->json($this->donorRepo->checkEligibility());
    }

    public function nearbyAdmins(Request $request)
    {
        return response()->json($this->donorRepo->findNearbyAdmins($request));
    }
}
