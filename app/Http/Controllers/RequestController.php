<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\interfaces\RequestRepositoryInterface;

class RequestController extends Controller
{
    protected $requestRepo;

    public function __construct(RequestRepositoryInterface $requestRepo)
    {
        $this->requestRepo = $requestRepo;
    }

    public function adminDashboard()
    {
        return response()->json($this->requestRepo->adminDashboard());
    }

    public function donorRequests()
    {
        return response()->json($this->requestRepo->donorRequests());
    }

    public function updateReceiverStatus(Request $request, $id)
    {
        $result = $this->requestRepo->updateReceiverStatus($request, $id);
        return response()->json(['success' => true, 'blood_request' => $result]);
    }

    public function updateDonorStatus(Request $request, $id)
    {
        $result = $this->requestRepo->updateDonorStatus($request, $id);
        return response()->json(['success' => true, 'donate_blood' => $result]);
    }

    public function history()
    {
        return response()->json($this->requestRepo->history());
    }
}
