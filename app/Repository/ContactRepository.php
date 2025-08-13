<?php
namespace App\Repository;

use App\Models\Contact;
use App\Repository\interfaces\ContactRepositoryInterface;

class ContactRepository implements ContactRepositoryInterface
{
    /**
     * Store a new contact message in the database.
     * @param array $data
     * @return Contact
     */
    public function create(array $data)
    {
        return Contact::create($data);
    }
}
