<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function findById(int $id)
    {
        return User::find($id);
    }

    public function findByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    public function findByAccount(string $account)
    {
        return User::where('account', $account)->first();
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function update(int $id, array $data)
    {
        $user = $this->findById($id);
        if (!$user) {
            return false;
        }

        return $user->update($data);
    }

    public function delete(int $id)
    {
        $user = $this->findById($id);
        if (!$user) {
            return false;
        }

        return $user->delete();
    }

    public function updateByAccount(string $account, array $data)
    {
        $user = $this->findByAccount($account);
        if (!$user) {
            return false;
        }

        return $user->update($data);
    }

    public function activate(User $user)
    {
        return $user->update([
            'email_verified_at' => now(),
            'verify_code' => null
        ]);
    }

    public function updateVerificationCode(User $user, string $code)
    {
        return $user->update([
            'verify_code' => $code,
            'verify_code_sent_at' => now()
        ]);
    }

    public function checkVerificationCode(string $account, string $code)
    {
        return User::where('account', $account)
            ->where('verify_code', $code)
            ->exists();
    }
}