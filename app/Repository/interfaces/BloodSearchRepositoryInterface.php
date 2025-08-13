<?php

namespace App\Repository\interfaces;

interface BloodSearchRepositoryInterface
{
    public function getNearbyAdmins(float $latitude, float $longitude);
    public function validateBloodRequest(array $data);
    public function storeRequestForm($file);
    public function createBloodRequest(array $data);
    public function prepareEsewaPayment(array $data);
    public function verifyEsewaTransaction(string $oid, float $amount);
    public function calculateDistanceBetweenPoints(float $lat1, float $lon1, float $lat2, float $lon2);
    public function getBloodRequestFromSession($request);
    public function storeBloodRequestInSession($request, array $data);
    public function clearBloodRequestSession($request);
}