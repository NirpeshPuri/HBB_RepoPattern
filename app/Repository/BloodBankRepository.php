<?php
namespace App\Repository;

use App\Models\BloodBank;
use App\Repository\interfaces\BloodBankRepositoryInterface;

class BloodBankRepository implements BloodBankRepositoryInterface
{
    /**
     * Get the current admin's blood bank.
     * Returns a BloodBank model instance.
     */
    public function getCurrentAdminBank()
    {
        return BloodBank::currentAdminBank();
    }

    /**
     * Update blood stock for a given blood type and quantity.
     * @param string $bloodType
     * @param int $quantity
     * @return bool
     */
    public function updateStock(string $bloodType, int $quantity): bool
    {
        $bank = $this->getCurrentAdminBank();
        return $bank->updateStock($bloodType, $quantity);
    }
}
