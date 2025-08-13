<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repository\Interfaces\AdminRepositoryInterface;
use Illuminate\Http\Request;

class AdminApiController extends Controller 
{
    private $adminRepo;

    public function __construct(AdminRepositoryInterface $adminRepo) 
    {
        $this->adminRepo = $adminRepo;
        $this->middleware('auth:api'); // Use API guard
    }

    // Example API endpoint
    public function pendingBloodRequests()
    {
        return response()->json(
            $this->adminRepo->getPendingBloodRequestsWithPriority()
        );
    }
}