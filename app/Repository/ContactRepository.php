<?php

namespace App\Repository;

use App\Models\contactModel;

class ContactRepository
{
    public function createContactReport($contactData)
    {
        return contactModel::create([
            'name' => $contactData['name'],
            'email' => $contactData['email'],
            'phone' => $contactData['phone'],
            'information' => $contactData['information'],
        ]);
    }

    public function getAllContacts()
    {
        return contactModel::all();
    }

    public function findContactById($id)
    {
        return contactModel::find($id);
    }
}