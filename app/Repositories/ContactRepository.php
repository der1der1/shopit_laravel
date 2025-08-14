<?php

namespace App\Repositories;

use App\Models\contactModel as Contact;

class ContactRepository
{
    public function create(array $data)
    {
        return Contact::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'information' => $data['information']
        ]);
    }

    public function findById(int $id)
    {
        return Contact::find($id);
    }

    public function getAllContacts()
    {
        return Contact::all();
    }

    public function delete(int $id)
    {
        $contact = $this->findById($id);
        if (!$contact) {
            return false;
        }
        
        return $contact->delete();
    }
}