<?php

namespace App\Repository;

use App\Models\Admin;
use App\Models\BloodRequest;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class BloodSearchRepository implements \App\Repository\interfaces\BloodSearchRepositoryInterface
{
    public function getNearbyAdmins(float $latitude, float $longitude)
    {
        return Admin::select(['id', 'name', 'latitude', 'longitude'])
            ->get()
            ->map(function ($admin) use ($latitude, $longitude) {
                $admin->distance = $this->calculateDistanceBetweenPoints(
                    $latitude,
                    $longitude,
                    $admin->latitude,
                    $admin->longitude
                );
                return $admin;
            })
            ->sortBy('distance')
            ->values();
    }

    public function validateBloodRequest(array $data)
    {
        $validator = Validator::make($data, [
            'admin_id' => 'required|exists:admins,id',
            'blood_group' => 'required|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
            'blood_quantity' => 'required|integer|min:1|max:5',
            'request_type' => [
                'required',
                'in:Emergency,Normal,Rare',
                function ($attribute, $value, $fail) use ($data) {
                    $rareBloodTypes = ['AB-', 'B-', 'A-'];
                    if ($value === 'Rare' && !in_array($data['blood_group'], $rareBloodTypes)) {
                        $fail('Rare request type can only be selected for rare blood types (AB-, B-, A-)');
                    }
                }
            ],
            'request_form' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'payment' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()];
        }

        return ['success' => true, 'data' => $validator->validated()];
    }

    public function storeRequestForm($file)
    {
        return $file->store('request_forms', 'public');
    }

    public function createBloodRequest(array $data)
    {
        return BloodRequest::create($data);
    }

    public function prepareEsewaPayment(array $data)
    {
        $transaction_uuid = uniqid('txn_') . time();
        $secret_key = "8gBm/:&EnhH.1/q";
        $product_code = "EPAYTEST";

        $signature_string = "total_amount={$data['payment']},transaction_uuid={$transaction_uuid},product_code={$product_code}";
        
        return [
            'transaction_uuid' => $transaction_uuid,
            'signature' => base64_encode(hash_hmac('sha256', $signature_string, $secret_key, true)),
            'product_code' => $product_code,
            'amount' => $data['payment']
        ];
    }

    public function verifyEsewaTransaction(string $oid, float $amount)
    {
        if (app()->environment('local', 'testing')) {
            return true;
        }

        try {
            $client = new Client();
            $response = $client->request('POST', "https://rc-epay.esewa.com.np/api/epay/transaction/status/".$oid, [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => [
                    'transaction_uuid' => $oid,
                    'total_amount' => $amount
                ]
            ]);

            $responseData = json_decode($response->getBody(), true);
            return isset($responseData['status']) && 
                   $responseData['status'] === 'COMPLETE' &&
                   $responseData['total_amount'] == $amount &&
                   $responseData['transaction_uuid'] == $oid;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function calculateDistanceBetweenPoints(float $lat1, float $lon1, float $lat2, float $lon2)
    {
        $earthRadius = 6371;
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    public function getBloodRequestFromSession($request)
    {
        return $request->session()->get('blood_request');
    }

    public function storeBloodRequestInSession($request, array $data)
    {
        $request->session()->put('blood_request', $data);
    }

    public function clearBloodRequestSession($request)
    {
        $request->session()->forget('blood_request');
    }
}