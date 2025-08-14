<?php

namespace App\Repository\interfaces;

interface RegisterRepositoryInterface
{
    public function createUser(array $userData);
    public function validateUserData(array $data): array;
}
