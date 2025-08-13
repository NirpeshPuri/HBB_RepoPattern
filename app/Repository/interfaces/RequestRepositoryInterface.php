<?php

namespace App\Repository\interfaces;

use Illuminate\Http\Request;

interface RequestRepositoryInterface
{
    public function adminDashboard();
    public function donorRequests();
    public function updateReceiverStatus(Request $request, $id);
    public function updateDonorStatus(Request $request, $id);
    public function history();
}
