<?php
namespace App\Repository;

use App\Models\BloodRequest;
use App\Models\Contact;
use App\Models\DonateBlood;
use App\Models\BloodBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Admin;
use App\Repository\interfaces\AdminRepositoryInterface;

class AdminRepository implements AdminRepositoryInterface
{
    public function getPendingBloodRequestsWithPriority()
    {
        return BloodRequest::with(['user', 'admin'])
            ->where('status', 'pending')
            ->orderByRaw("
                CASE
                    WHEN request_type = 'Emergency' THEN 0
                    WHEN request_type = 'Rare' THEN 1
                    ELSE 2
                END
            ")
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPendingDonorRequests()
    {
        return DonateBlood::with(['user', 'admin'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function updateReceiverStatus($id, $status, $adminId, $bloodGroup = null, $quantity = null)
    {
        return DB::transaction(function () use ($id, $status, $adminId, $bloodGroup, $quantity) {
            $bloodRequest = BloodRequest::findOrFail($id);
            
            if ($status === 'approved' && $bloodGroup && $quantity) {
                $bloodBank = BloodBank::currentAdminBank();
                $bloodBank->updateStock($bloodGroup, -$quantity);
            }
            
            $bloodRequest->update([
                'status' => $status,
                'admin_id' => $adminId
            ]);
            
            return $bloodRequest;
        });
    }

    public function updateDonorStatus($id, $status, $adminId, $bloodType = null, $quantity = null)
    {
        return DB::transaction(function () use ($id, $status, $adminId, $bloodType, $quantity) {
            $donorRequest = DonateBlood::findOrFail($id);
            
            if ($status === 'approved' && $bloodType && $quantity) {
                $bloodBank = BloodBank::currentAdminBank();
                $bloodBank->updateStock($bloodType, $quantity);
            }
            
            $donorRequest->update([
                'status' => $status,
                'admin_id' => $adminId
            ]);
            
            return $donorRequest;
        });
    }

    public function getCurrentAdminBloodBank()
    {
        return BloodBank::currentAdminBank();
    }

    public function getAdminProfile($adminId)
    {
        return Admin::findOrFail($adminId);
    }

    public function updateAdminProfile($adminId, array $data)
    {
        $admin = Admin::findOrFail($adminId);
        $admin->update($data);
        return $admin;
    }

    public function getAllUserReports()
    {
        return Contact::with(['user', 'admin'])->get();
    }
}