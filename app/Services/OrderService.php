<?php

namespace App\Services;

use App\Repositories\PurchasedRepository;
use App\Repositories\UserRepository;
use App\Services\EmailService;
use App\Services\ProductService;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    protected $purchasedRepository;
    protected $userRepository;
    protected $emailService;
    protected $productService;

    public function __construct(
        PurchasedRepository $purchasedRepository,
        UserRepository $userRepository,
        EmailService $emailService,
        ProductService $productService
    ) {
        $this->purchasedRepository = $purchasedRepository;
        $this->userRepository = $userRepository;
        $this->emailService = $emailService;
        $this->productService = $productService;
    }

    public function addToCart(string $productId)
    {
        $user = $this->userRepository->findByAccount(Auth::user()->account);
        if (!$user) {
            return false;
        }

        $want = $user->want ?? '';
        $want .= $productId . ',';
        
        return $this->userRepository->update($user->id, ['want' => $want]);
    }

    public function getLastOrder(string $account)
    {
        $order = $this->purchasedRepository->getLastOrderByAccount($account);
        if (!$order) {
            return null;
        }

        $products = $this->getPurchasedProducts($order->purchased);
        
        return [
            'order' => $order,
            'products' => $products,
            'user' => $this->userRepository->findByAccount($account)
        ];
    }

    public function getPurchasedProducts(string $purchased)
    {
        $purchases = explode(';', $purchased);
        $products = [];

        foreach ($purchases as $purchase) {
            $purchaseData = explode(',', $purchase);
            if (count($purchaseData) !== 2) continue;

            $product = $this->productService->getProductById($purchaseData[0]);
            if (!$product) continue;

            $products[] = [
                'id' => $purchaseData[0],
                'num' => $purchaseData[1],
                'pic_dir' => $product->pic_dir,
                'product_name' => $product->product_name,
                'description' => $product->description,
                'price' => $product->price,
            ];
        }

        return $products;
    }

    public function updateOrderDelivery(string $account, array $data)
    {
        $order = $this->purchasedRepository->getLastOrderByAccount($account);
        $user = $this->userRepository->findByAccount($account);

        if (!$order || !$user) {
            return false;
        }

        // 更新訂單和用戶的送貨資訊
        $orderData = [];
        $userData = [];

        if (isset($data['store'])) {
            $orderData['to_shop'] = $data['store'];
            $orderData['shop1_addr2'] = '1';
            $userData['to_shop'] = $data['store'];
        }

        if (isset($data['address'])) {
            $orderData['to_address'] = $data['address'];
            $orderData['shop1_addr2'] = '2';
            $userData['to_address'] = $data['address'];
        }

        if (isset($data['name_input'])) {
            $orderData['name'] = $data['name_input'];
            $userData['name'] = $data['name_input'];
        }

        if (isset($data['account_input'])) {
            $orderData['bank_account'] = $data['account_input'];
            $userData['bank_account'] = $data['account_input'];
        }

        $this->purchasedRepository->update($order->id, $orderData);
        $this->userRepository->update($user->id, $userData);

        return true;
    }

    public function confirmOrder(string $account)
    {
        $order = $this->purchasedRepository->getLastOrderByAccount($account);
        $user = $this->userRepository->findByAccount($account);
        
        if (!$order || !$user) {
            return false;
        }

        // 更新訂單狀態
        $this->purchasedRepository->update($order->id, ['show' => '1']);

        // 更新用戶通知
        $newInfo = $this->formatNewNotification($user->info0);
        $this->userRepository->update($user->id, ['info0' => $newInfo]);

        // 發送確認郵件
        $products = $this->getPurchasedProducts($order->purchased);
        $this->emailService->sendPurchaseConfirmation($account, $products, $order->toArray());

        return true;
    }

    protected function formatNewNotification(?string $currentInfo): string
    {
        $newMessage = "您的訂單已送出！" . date("Y/m/d H:i:s");
        
        if (empty($currentInfo)) {
            return $newMessage;
        }

        $messages = explode(';', $currentInfo);
        array_unshift($messages, $newMessage);
        $messages = array_slice($messages, 0, 3);
        
        return implode(';', $messages);
    }
}