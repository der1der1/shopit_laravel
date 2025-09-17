<?php

namespace App\Service;

use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AdminOrderService
{
    protected $orderRepository;
    protected $productRepository;
    protected $userRepository;

    public function __construct(
        OrderRepository $orderRepository,
        ProductRepository $productRepository,
        UserRepository $userRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
    }

    public function getAdminOrderList()
    {
        $user = Auth::user();
        
        // 檢查權限
        if ($user->prvilige !== "A") {
            return ['error' => '您沒有權限執行此操作', 'redirect' => 'home'];
        }

        $marqee = $this->productRepository->getAllMarqee();
        $orders = $this->orderRepository->getOrdersForAdmin();
        
        $processedOrders = $this->processOrdersForDisplay($orders);

        return [
            'success' => true,
            'user' => $user,
            'marqee' => $marqee,
            'new_lists' => $processedOrders
        ];
    }

    private function processOrdersForDisplay($orders)
    {
        $processedOrders = [];

        foreach ($orders as $order) {
            $purchasedProducts = $this->parsePurchasedItems($order->purchased);
            
            $processedOrders[] = [
                'id' => $order->id,
                'account' => $order->account,
                'name' => $order->name,
                'to_shop' => $order->to_shop,
                'to_address' => $order->to_address,
                'shop1_addr2' => $order->shop1_addr2,
                'product' => $purchasedProducts,
            ];
        }

        return $processedOrders;
    }

    private function parsePurchasedItems($purchased)
    {
        $singleProducts = explode(';', $purchased);
        $purchasedProducts = [];

        foreach ($singleProducts as $singleProduct) {
            $productData = explode(',', $singleProduct);
            $product = $this->productRepository->findProductById($productData[0]);
            
            if ($product) {
                $purchasedProducts[] = [
                    'id' => $product->id,
                    'product_name' => $product->product_name,
                    'price' => $product->price,
                    'num' => $productData[1],
                ];
            }
        }

        return $purchasedProducts;
    }

    public function processOrderDelivery(Request $request)
    {
        $orderId = $request->id_done;
        $userAccount = $request->account_done;

        // 更新訂單狀態
        $orderUpdates = [
            'payed' => "1",
            'delivered' => "1",
            'show' => "0"
        ];
        
        $order = $this->orderRepository->updateOrderStatus($orderId, $orderUpdates);

        // 更新用戶通知
        $this->userRepository->updateUserNotification($userAccount, "訂購商品已寄出！");

        return [
            'success' => '單號：' . $order->id . '  商品已寄出！',
            'redirect' => 'list_show'
        ];
    }
}