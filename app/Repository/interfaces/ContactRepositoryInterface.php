<?php
namespace App\Repository\interfaces;

interface ContactRepositoryInterface
{
    /**
     * Store a new contact message in the database.
     * @param array $data
     * @return mixed
     */
    public function create(array $data);
}
