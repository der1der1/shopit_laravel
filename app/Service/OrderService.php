<?php

namespace App\Service;

use App\Repository\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class OrderService
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getUserOrders(Request $request)
    {
        $order_query = $request->input('order_query');
        $user = User::find(Auth::id());

        // 查詢訂單
        $orders = $this->orderRepository->findOrdersByUser($user, $order_query);

        if ($orders->isEmpty()) {
            return ['error' => '沒有找到相關訂單', 'status' => 404];
        }

        // 整理訂單資料
        $processedOrders = $this->processOrderData($orders);

        return ['orders' => $processedOrders, 'status' => 200];
    }

    private function processOrderData($orders)
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
                    continue; // 跳過格式不正確的資料
                }
                
                $product = $this->orderRepository->findProductById($purchased[0]);
                if (!$product) {
                    continue; // 跳過找不到商品的資料
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