<?php
namespace App\Repository;

use App\Models\Contact;
use App\Repository\interfaces\ContactRepositoryInterface;

class ContactRepository implements ContactRepositoryInterface
{
    public function create(array $data)
    {
        return Contact::create($data);
    }
}
