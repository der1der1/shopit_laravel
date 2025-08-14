<?php

namespace App\Repositories;

use App\Models\purchasedModel as Order;
use App\Models\productsModel as Product;

class OrderRepository
{
    public function findByAccount(string $account, ?string $orderId = null)
    {
        $query = Order::where('account', $account);
        
        if ($orderId) {
            $query->where('id', $orderId);
        }

        $orders = $query->get();

        return $this->formatOrders($orders);
    }

    private function formatOrders($orders)
    {
        foreach ($orders as $order) {
            $purchaseds = $order->purchased;
            if (!$purchaseds) {
                $order->purchased = [];
                continue;
            }

            $purchaseds = explode(';', $purchaseds);
            $ordered_purchaseds = [];
            
            foreach ($purchaseds as $purchased) {
                $purchased = explode(',', $purchased);
                if (count($purchased) < 3) {
                    continue;
                }
                
                $product = Product::where('id', $purchased[0])->first();
                if (!$product) {
                    continue;
                }
                
                $ordered_purchaseds[] = [
                    'product_name' => $product->product_name,
                    'number' => $purchased[1],
                    'price' => $purchased[2]
                ];
            }
            
            $order->purchased = $ordered_purchaseds;
        }

        return $orders;
    }
}