<?php

namespace App\Repository;

use App\Repository\interfaces\ReceiverStatusRepositoryInterface;
use App\Models\BloodRequest;
use App\Models\BloodBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ReceiverStatusRepository implements ReceiverStatusRepositoryInterface
{
    public function index()
    {
        $requests = BloodRequest::where('user_id', Auth::id())
            ->with('admin')
            ->orderBy('created_at', 'desc')
            ->get();

        return $requests;
    }

    public function edit($id)
    {
        $request = BloodRequest::where('user_id', Auth::id())->findOrFail($id);

        if (!$request->canEdit()) {
            return null; // Caller can handle this
        }

        $bloodBank = BloodBank::where('admin_id', $request->admin_id)->first();

        $stock = [
            'A+' => $bloodBank->{'A+'} ?? 0,
            'A-' => $bloodBank->{'A-'} ?? 0,
            'B+' => $bloodBank->{'B+'} ?? 0,
            'B-' => $bloodBank->{'B-'} ?? 0,
            'AB+' => $bloodBank->{'AB+'} ?? 0,
            'AB-' => $bloodBank->{'AB-'} ?? 0,
            'O+' => $bloodBank->{'O+'} ?? 0,
            'O-' => $bloodBank->{'O-'} ?? 0,
        ];

        return [
            'request' => $request,
            'currentFileUrl' => $request->file_url,
            'adminId' => $request->admin_id,
            'stock' => $stock,
        ];
    }

    public function update(Request $request, $id)
    {
        $bloodRequest = BloodRequest::where('user_id', Auth::id())->findOrFail($id);

        if (!$bloodRequest->canEdit()) {
            return ['success' => false, 'message' => 'Cannot update this request.'];
        }

        $validated = $request->validate([
            'blood_group' => 'required|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
            'blood_quantity' => 'required|integer|min:1',
            'request_type' => 'required|in:Emergency,Rare,Normal',
            'request_form' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'payment' => 'required|numeric|min:0',
        ]);

        if ($request->hasFile('request_form')) {
            if ($bloodRequest->request_form && file_exists(public_path($bloodRequest->request_form))) {
                File::delete(public_path($bloodRequest->request_form));
            }

            $imageName = time().'.'.$request->file('request_form')->getClientOriginalExtension();
            $request->file('request_form')->move(public_path('assets/request_forms'), $imageName);
            $validated['request_form'] = 'assets/request_forms/'.$imageName;
        }

        $bloodRequest->update($validated);

        return ['success' => true, 'message' => 'Request updated successfully.'];
    }

    public function destroy($id)
    {
        $bloodRequest = BloodRequest::where('user_id', Auth::id())->findOrFail($id);

        if (!$bloodRequest->canEdit()) {
            return ['success' => false, 'message' => 'Cannot delete this request.'];
        }

        if ($bloodRequest->request_form && file_exists(public_path($bloodRequest->request_form))) {
            File::delete(public_path($bloodRequest->request_form));
        }

        $bloodRequest->delete();

        return ['success' => true, 'message' => 'Request deleted successfully.'];
    }
}
