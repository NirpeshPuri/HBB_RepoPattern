<?php

namespace App\Repository\interfaces;

interface BloodSearchRepositoryInterface
{
    public function getUserBloodRequests(int $userId);
    public function getUserBloodRequestById(int $userId, int $requestId);
    public function createBloodRequest(array $data);
    public function updateBloodRequest(int $requestId, array $data);
    public function deleteBloodRequest(int $requestId);

    public function findNearbyAdmins(float $latitude, float $longitude);
    public function validateBloodRequest(array $data, bool $isUpdate = false);
    public function storeRequestForm($file);
}
