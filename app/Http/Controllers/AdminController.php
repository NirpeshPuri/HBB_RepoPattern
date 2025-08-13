<?php

namespace App\Http\Controllers;

use App\Repository\interfaces\AdminRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    private $adminRepo;

    public function __construct(AdminRepositoryInterface $adminRepo)
    {
        $this->adminRepo = $adminRepo;
    }

    // GET /admin/dashboard
    public function dashboard()
    {
        $requests = $this->adminRepo->getPendingBloodRequestsWithPriority();
        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    // GET /admin/donor-requests
    public function donorRequests()
    {
        $requests = $this->adminRepo->getPendingDonorRequests();
        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    // PATCH /admin/receiver-status/{id}
    public function updateReceiverStatus(Request $request, $id)
    {
        $request->validate(['action' => 'required|in:approve,reject']);

        try {
            $status = $request->action === 'approve' ? 'approved' : 'rejected';
            $adminId = Auth::id();

            $bloodRequest = $this->adminRepo->updateReceiverStatus(
                $id,
                $status,
                $adminId,
                $status === 'approved' ? $bloodRequest->blood_group : null,
                $status === 'approved' ? $bloodRequest->blood_quantity : null
            );

            return response()->json([
                'success' => true,
                'message' => "Request {$status} successfully",
                'data' => $bloodRequest
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // PATCH /admin/donor-status/{id}
    public function updateDonorStatus(Request $request, $id)
    {
        $request->validate(['action' => 'required|in:approve,reject']);

        try {
            $status = $request->action === 'approve' ? 'approved' : 'rejected';
            $adminId = Auth::id();

            $donorRequest = $this->adminRepo->updateDonorStatus(
                $id,
                $status,
                $adminId,
                $status === 'approved' ? $donorRequest->blood_type : null,
                $status === 'approved' ? $donorRequest->blood_quantity : null
            );

            return response()->json([
                'success' => true,
                'message' => "Donor request {$status} successfully",
                'data' => $donorRequest
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // GET /admin/blood-inventory
    public function showBloodInventory()
    {
        try {
            $bloodBank = $this->adminRepo->getCurrentAdminBloodBank();
            return response()->json([
                'success' => true,
                'data' => $bloodBank
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // GET /admin/profile
    public function showProfileUpdateForm()
    {
        $admin = $this->adminRepo->getAdminProfile(Auth::id());
        return response()->json([
            'success' => true,
            'data' => $admin
        ]);
    }

    // PATCH /admin/profile
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z ]+$/',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('admins')->ignore(Auth::id()),
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ],
            'phone' => 'required|string|digits:10|regex:/^[0-9]{10}$/',
            'address' => 'required|string|max:255|regex:/^[a-zA-Z0-9\s,.-]+$/',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'password' => 'nullable|confirmed|min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/'
        ]);

        try {
            if (empty($validated['password'])) {
                unset($validated['password']);
            } else {
                $validated['password'] = bcrypt($validated['password']);
            }

            $this->adminRepo->updateAdminProfile(Auth::id(), $validated);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully!'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile. ' . $e->getMessage()
            ], 400);
        }
    }

    // GET /admin/report
    public function report()
    {
        $requests = $this->adminRepo->getAllUserReports();
        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }
}
