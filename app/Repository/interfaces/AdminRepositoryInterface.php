<?php
// app/Repository/Interfaces/AdminRepositoryInterface.php
namespace App\Repository\interfaces;

interface AdminRepositoryInterface
{
    // Dashboard Methods
    public function getPendingBloodRequestsWithPriority();
    
    // Donor Requests
    public function getPendingDonorRequests();
    
    // Blood Request Status Updates
    public function updateReceiverStatus($id, $status, $adminId, $bloodGroup = null, $quantity = null);
    
    // Donor Status Updates
    public function updateDonorStatus($id, $status, $adminId, $bloodType = null, $quantity = null);
    
    // Blood Inventory
    public function getCurrentAdminBloodBank();
    
    // Profile
    public function getAdminProfile($adminId);
    public function updateAdminProfile($adminId, array $data);
    
    // Reports
    public function getAllUserReports();
}