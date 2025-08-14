<?php

namespace App\Repositories;

use App\Models\mailListModel;

class MailListRepository
{
    public function getActiveAdminEmails()
    {
        return mailListModel::where('onoff', 1)
            ->where('id', '!=', 1)
            ->pluck('email')
            ->toArray();
    }

    public function findById(int $id)
    {
        return mailListModel::find($id);
    }

    public function create(array $data)
    {
        return mailListModel::create($data);
    }

    public function update(int $id, array $data)
    {
        $mailList = $this->findById($id);
        if (!$mailList) {
            return false;
        }
        
        return $mailList->update($data);
    }

    public function delete(int $id)
    {
        $mailList = $this->findById($id);
        if (!$mailList) {
            return false;
        }
        
        return $mailList->delete();
    }
}