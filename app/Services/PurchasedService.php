<?php

namespace App\Services;

use App\Repositories\PurchasedRepository;
use App\Repositories\ProductRepository;

class PurchasedService
{
    protected $purchasedRepository;
    protected $productRepository;

    public function __construct(
        PurchasedRepository $purchasedRepository,
        ProductRepository $productRepository
    ) {
        $this->purchasedRepository = $purchasedRepository;
        $this->productRepository = $productRepository;
    }

    public function getVisibleOrders()
    {
        $orders = $this->purchasedRepository->getVisibleOrders();
        $formattedOrders = [];

        foreach ($orders as $order) {
            $singleProducts = explode(';', $order->purchased);
            $purchasedProducts = [];

            foreach ($singleProducts as $singleProduct) {
                $productInfo = explode(',', $singleProduct);
                $product = $this->productRepository->findById($productInfo[0]);
                
                if ($product) {
                    $purchasedProducts[] = [
                        'id' => $product->id,
                        'product_name' => $product->product_name,
                        'price' => $product->price,
                        'num' => $productInfo[1],
                    ];
                }
            }

            $formattedOrders[] = [
                'id' => $order->id,
                'account' => $order->account,
                'name' => $order->name,
                'to_shop' => $order->to_shop,
                'to_address' => $order->to_address,
                'shop1_addr2' => $order->shop1_addr2,
                'product' => $purchasedProducts,
            ];
        }

        return $formattedOrders;
    }

    public function completeOrder(int $orderId, string $account)
    {
        // 更新訂單狀態
        $status = [
            'payed' => '1',
            'delivered' => '1',
            'show' => '0'
        ];
        
        $updated = $this->purchasedRepository->updateOrderStatus($orderId, $status);
        
        if (!$updated) {
            return false;
        }

        // 更新用戶通知訊息
        $notification = $this->formatNewNotification($account);
        $this->purchasedRepository->updateUserInfo($account, $notification);

        return true;
    }

    protected function formatNewNotification(string $account): string
    {
        $currentUser = $this->purchasedRepository->getUserByAccount($account);
        $newMessage = "訂購商品已寄出！" . date("Y/m/d H:i:s");
        
        if (!$currentUser || empty($currentUser->info0)) {
            return $newMessage;
        }

        $notifications = explode(';', $currentUser->info0);
        array_unshift($notifications, $newMessage);
        
        // 保持最多三則訊息
        $notifications = array_slice($notifications, 0, 3);
        
        return implode(';', $notifications);
    }
}