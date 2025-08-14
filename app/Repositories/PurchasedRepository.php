<?php

namespace App\Repositories;

use App\Models\purchasedModel;

class PurchasedRepository
{
    public function findById(int $id)
    {
        return purchasedModel::find($id);
    }

    public function getVisibleOrders()
    {
        return purchasedModel::where('show', "1")->get();
    }

    public function create(array $data)
    {
        return purchasedModel::create($data);
    }

    public function update(int $id, array $data)
    {
        $purchased = $this->findById($id);
        if (!$purchased) {
            return false;
        }

        return $purchased->update($data);
    }

    public function delete(int $id)
    {
        $purchased = $this->findById($id);
        if (!$purchased) {
            return false;
        }

        return $purchased->delete();
    }

    public function getLastOrderByAccount(string $account)
    {
        return purchasedModel::where('account', $account)
            ->orderBy('id', 'desc')
            ->first();
    }

    public function updateOrderStatus(int $id, array $status)
    {
        $order = $this->findById($id);
        if (!$order) {
            return false;
        }

        return $order->update($status);
    }

    public function getUserOrders(string $account)
    {
        return purchasedModel::where('account', $account)
            ->orderBy('id', 'desc')
            ->get();
    }

    public function updateUserInfo(string $account, string $newInfo)
    {
        $purchased = $this->getLastOrderByAccount($account);
        if (!$purchased) {
            return false;
        }

        return $purchased->update(['info0' => $newInfo]);
    }

    public function getOrderWithProducts(int $id)
    {
        return purchasedModel::with('products')->find($id);
    }
}