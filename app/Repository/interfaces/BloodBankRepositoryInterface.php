<?php
namespace App\Repository\interfaces;

interface BloodBankRepositoryInterface
{
    // Get the current admin's blood bank
    public function getCurrentAdminBank();

    // Update blood stock for a given blood type and quantity
    public function updateStock(string $bloodType, int $quantity): bool;
}
