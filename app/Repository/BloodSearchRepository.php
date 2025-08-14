<?php

namespace App\Repository;

use App\Models\BloodRequest;
use App\Models\Admin;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Repository\interfaces\BloodSearchRepositoryInterface;

class BloodSearchRepository implements BloodSearchRepositoryInterface
{
    public function getUserBloodRequests(int $userId)
    {
        return BloodRequest::where('user_id', $userId)->get();
    }

    public function getUserBloodRequestById(int $userId, int $requestId)
    {
        return BloodRequest::where('user_id', $userId)->where('id', $requestId)->first();
    }

    public function createBloodRequest(array $data)
    {
        return BloodRequest::create($data);
    }

    public function updateBloodRequest(int $requestId, array $data)
    {
        $request = BloodRequest::find($requestId);
        if (!$request) {
            return null;
        }

        $request->update($data);
        return $request;
    }

    public function deleteBloodRequest(int $requestId)
    {
        $request = BloodRequest::find($requestId);
        if (!$request) {
            return false;
        }

        return $request->delete();
    }

    public function findNearbyAdmins(float $latitude, float $longitude)
    {
        $admins = Admin::select(['id', 'name', 'latitude', 'longitude'])->get();
        return $admins
            ->map(function ($admin) use ($latitude, $longitude) {
                $admin->distance = $this->calculateDistance($latitude, $longitude, $admin->latitude, $admin->longitude);
                return $admin;
            })
            ->sortBy('distance')
            ->values();
    }

    public function validateBloodRequest(array $data, bool $isUpdate = false)
    {
        $rules = [
            'admin_id' => 'required|exists:admins,id',
            'blood_group' => 'required|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
            'blood_quantity' => 'required|integer|min:1|max:5',
            'request_type' => ['required', 'in:Emergency,Normal,Rare'],
            'request_form' => $isUpdate ? 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048' : 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()];
        }

        return ['success' => true, 'data' => $validator->validated()];
    }

    public function storeRequestForm($file)
    {
        return $file->store('request_forms', 'public');
    }

    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2)
    {
        $earthRadius = 6371;
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }
}
