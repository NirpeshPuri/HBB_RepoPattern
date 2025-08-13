<?php

namespace App\Repository;

use App\Repository\interfaces\RequestRepositoryInterface;
use App\Models\BloodRequest;
use App\Models\DonateBlood;
use App\Models\BloodBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequestRepository implements RequestRepositoryInterface
{
    public function adminDashboard()
    {
        return BloodRequest::with(['user', 'admin'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function donorRequests()
    {
        return DonateBlood::with(['user', 'admin'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function updateReceiverStatus(Request $request, $id)
{
    return DB::transaction(function () use ($request, $id) {

        $bloodRequest = BloodRequest::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:Approved,Rejected,Pending',
            'payment' => 'nullable|numeric'
        ]);

        $bloodBank = BloodBank::where('admin_id', Auth::id())->first();

        if ($validated['status'] == 'Approved' && $bloodRequest->status != 'Approved') {
            if (!$bloodBank) {
                // Return JSON error immediately without referencing $bloodRequest
                return response()->json(['error' => 'Blood bank not found'], 404);
            }

            $currentQuantity = $bloodBank->{$bloodRequest->blood_group} ?? 0;
            if ($currentQuantity < $bloodRequest->blood_quantity) {
                return response()->json(['error' => 'Not enough blood in stock'], 422);
            }

            $bloodBank->{$bloodRequest->blood_group} = $currentQuantity - $bloodRequest->blood_quantity;
            $bloodBank->save();
        } elseif ($bloodRequest->status == 'Approved' && $validated['status'] != 'Approved') {
            if ($bloodBank) {
                $currentQuantity = $bloodBank->{$bloodRequest->blood_group} ?? 0;
                $bloodBank->{$bloodRequest->blood_group} = $currentQuantity + $bloodRequest->blood_quantity;
                $bloodBank->save();
            }
        }

        // Update the request status after checking stock
        $bloodRequest->update([
            'status' => $validated['status'],
            'payment' => $validated['payment'] ?? null,
            'admin_id' => Auth::id()
        ]);

        // Always return $bloodRequest
        return $bloodRequest;
    });
}


    public function updateDonorStatus(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $donateBlood = DonateBlood::findOrFail($id);

            $validated = $request->validate([
                'status' => 'required|in:Approved,Rejected,Pending',
                'donation_date' => 'nullable|date'
            ]);

            $bloodBank = BloodBank::firstOrCreate(
                ['admin_id' => Auth::id()],
                ['admin_name' => Auth::user()->name]
            );

            if ($validated['status'] == 'Approved' && $donateBlood->status != 'Approved') {
                $currentQuantity = $bloodBank->{$donateBlood->blood_group} ?? 0;
                $bloodBank->{$donateBlood->blood_group} = $currentQuantity + $donateBlood->blood_quantity;
                $bloodBank->save();
            } elseif ($donateBlood->status == 'Approved' && $validated['status'] != 'Approved') {
                $currentQuantity = $bloodBank->{$donateBlood->blood_group} ?? 0;
                $bloodBank->{$donateBlood->blood_group} = max(0, $currentQuantity - $donateBlood->blood_quantity);
                $bloodBank->save();
            }

            $donateBlood->update([
                'status' => $validated['status'],
                'donation_date' => $validated['donation_date'] ?? null,
                'admin_id' => Auth::id()
            ]);

            return $donateBlood;
        });
    }

    public function history()
    {
        $admin = Auth::guard('admin')->user();

        $requests = BloodRequest::with(['user', 'admin'])
            ->where('admin_id', $admin->id)
            ->get();

        $donations = DonateBlood::with(['user', 'admin'])
            ->where('admin_id', $admin->id)
            ->get();

        $combinedHistory = collect();

        foreach ($requests as $request) {
            $combinedHistory->push([
                'type' => 'request',
                'id' => $request->id,
                'user_name' => $request->user_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'blood_group' => $request->blood_group,
                'blood_quantity' => $request->blood_quantity,
                'request_type' => $request->request_type,
                'status' => $request->status,
                'created_at' => $request->created_at,
                'payment' => $request->payment,
                'admin_id' => $request->admin_id
            ]);
        }

        foreach ($donations as $donation) {
            $combinedHistory->push([
                'type' => 'donation',
                'id' => $donation->id,
                'user_name' => $donation->user_name,
                'email' => $donation->email,
                'phone' => $donation->phone,
                'blood_type' => $donation->blood_type,
                'blood_quantity' => $donation->blood_quantity,
                'status' => $donation->status,
                'donation_date' => $donation->donation_date,
                'created_at' => $donation->created_at,
                'admin_id' => $donation->admin_id
            ]);
        }

        return $combinedHistory->sortByDesc(function($item) {
            return $item['type'] === 'donation' && isset($item['donation_date'])
                ? $item['donation_date']
                : $item['created_at'];
        })->values();
    }
}
