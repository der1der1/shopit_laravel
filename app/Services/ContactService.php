<?php

namespace App\Services;

use App\Repositories\ContactRepository;
use Illuminate\Support\Facades\Auth;

class ContactService
{
    protected $contactRepository;

    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    public function createContact(array $data)
    {
        try {
            // If user is logged in, we could log additional information
            if (Auth::check()) {
                $data['user_id'] = Auth::id();
            }

            $contact = $this->contactRepository->create($data);

            // Here we could add additional functionality like:
            // - Sending email notifications to admin
            // - Creating an internal notification
            // - Logging the contact request
            
            return $contact;
        } catch (\Exception $e) {
            throw new \Exception('無法建立聯絡表單：' . $e->getMessage());
        }
    }

    public function getAllContacts()
    {
        return $this->contactRepository->getAllContacts();
    }

    public function getContact(int $id)
    {
        return $this->contactRepository->findById($id);
    }

    public function deleteContact(int $id)
    {
        try {
            $result = $this->contactRepository->delete($id);
            if (!$result) {
                throw new \Exception('找不到指定的聯絡表單');
            }
            return true;
        } catch (\Exception $e) {
            throw new \Exception('無法刪除聯絡表單：' . $e->getMessage());
        }
    }
}