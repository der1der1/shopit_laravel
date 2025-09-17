<?php

namespace App\Repository;

use App\Models\purchasedModel as Order;
use App\Models\productsModel as Product;

class OrderRepository
{
    public function findOrdersByUser($user, $orderId = null)
    {
        if (empty($orderId)) {
            // 無單號搜尋全部
            return Order::where('account', $user->account)->get();
        } else {
            // 有單號搜尋單一
            return Order::where('account', $user->account)->where('id', $orderId)->get();
        }
    }

    public function findProductById($productId)
    {
        return Product::where('id', $productId)->first();
    }

    public function createPurchaseOrder($userAccount, $purchasedItems, $totalBill)
    {
        return Order::create([
            'account' => $userAccount,
            'purchased' => $purchasedItems,
            'bill' => $totalBill,
            'payed' => "0",
            'delivered' => "0",
            'recieved' => "0",
            'show' => "0",
        ]);
    }

    public function getOrdersForAdmin()
    {
        return Order::where('show', "1")->get();
    }

    public function getLatestOrderByAccount($userAccount)
    {
        return Order::where('account', $userAccount)->orderBy('id', 'desc')->first();
    }

    public function updateOrderStatus($orderId, $updates)
    {
        $order = Order::where('id', $orderId)->first();
        if ($order) {
            foreach ($updates as $field => $value) {
                $order->$field = $value;
            }
            $order->save();
        }
        return $order;
    }

    public function updateOrderDeliveryInfo($userAccount, $deliveryData)
    {
        $order = $this->getLatestOrderByAccount($userAccount);
        if ($order) {
            foreach ($deliveryData as $field => $value) {
                $order->$field = $value;
            }
            $order->save();
        }
        return $order;
    }
}