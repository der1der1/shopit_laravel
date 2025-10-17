<?php

namespace App\Service;

use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Service\EmailService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

class PaymentService
{
    protected $orderRepository;
    protected $productRepository;
    protected $userRepository;
    protected $emailService;

    public function __construct(
        OrderRepository $orderRepository,
        ProductRepository $productRepository,
        UserRepository $userRepository,
        EmailService $emailService
    ) {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
        $this->emailService = $emailService;
    }

    public function getPaymentPageData()
    {
        $user = Auth::user();
        $marqee = $this->productRepository->getAllMarqee();
        $userInfo = User::where('account', $user->account ?? $user->email)->first();
        $latestOrder = $this->orderRepository->getLatestOrderByAccount($user->account ?? $user->email);
        
        $products = $this->parsePurchasedProducts($latestOrder->purchased);

        return [
            'user' => $user,
            'marqee' => $marqee,
            'products' => $products,
            'ppl_info' => $userInfo,
            'purchased' => $latestOrder
        ];
    }

    private function parsePurchasedProducts($purchased)
    {
        $purchaseItems = explode(';', $purchased);
        $products = [];
        
        foreach ($purchaseItems as $purchase) {
            $purchaseData = explode(',', $purchase);
            $product = $this->productRepository->findProductById($purchaseData[0]);
            
            if ($product) {
                $products[] = [
                    'id' => $purchaseData[0],
                    'num' => $purchaseData[1],
                    'pic_dir' => $product->pic_dir,
                    'product_name' => $product->product_name,
                    'description' => $product->description,
                    'price' => $product->price,
                ];
            }
        }
        
        return $products;
    }

    public function addToWishlist(Request $request)
    {
        $userAccount = Auth::user()->account;
        $productId = $request->product_id;
        
        $this->userRepository->addToWishlist($userAccount, $productId);
        
        return ['success' => '加入成功', 'redirect' => 'home'];
    }

    public function updateDeliveryToStore(Request $request)
    {
        $userAccount = Auth::user()->account;
        $storeName = $request->store;
        
        // 更新訂單信息
        $orderData = [
            'to_shop' => $storeName,
            'shop1_addr2' => "1"
        ];
        $this->orderRepository->updateOrderDeliveryInfo($userAccount, $orderData);
        
        // 更新用戶信息
        $userData = [
            'to_shop' => $storeName
        ];
        $this->userRepository->updateUserDeliveryInfo($userAccount, $userData);
        
        return ['success' => '超商寄送到' . $storeName, 'redirect' => 'pay_show'];
    }

    public function updateDeliveryToHome(Request $request)
    {
        if (empty($request->address)) {
            return ['error' => '住家地址不可空白', 'redirect' => 'pay_show'];
        }
        
        $userAccount = Auth::user()->account;
        $address = $request->address;
        
        // 更新訂單信息
        $orderData = [
            'to_address' => $address,
            'shop1_addr2' => "2"
        ];
        $this->orderRepository->updateOrderDeliveryInfo($userAccount, $orderData);
        
        // 更新用戶信息
        $userData = [
            'to_address' => $address
        ];
        $this->userRepository->updateUserDeliveryInfo($userAccount, $userData);
        
        return ['success' => '宅配到：' . $address, 'redirect' => 'pay_show'];
    }

    public function updateRecipientName(Request $request)
    {
        if (empty($request->name_input)) {
            return ['error' => '請輸入姓名', 'redirect' => 'pay_show'];
        }
        
        $userAccount = Auth::user()->account;
        $name = $request->name_input;
        
        // 更新用戶名稱
        $userData = ['name' => $name];
        $this->userRepository->updateUserDeliveryInfo($userAccount, $userData);
        
        // 更新訂單名稱
        $orderData = ['name' => $name];
        $this->orderRepository->updateOrderDeliveryInfo($userAccount, $orderData);
        
        return ['success' => '取貨大名：' . $name, 'redirect' => 'pay_show'];
    }

    public function updateBankAccount(Request $request)
    {
        if (empty($request->account_input)) {
            return ['error' => '請輸入正確的扣款帳號', 'redirect' => 'pay_show'];
        }
        
        $userAccount = Auth::user()->account;
        $bankAccount = $request->account_input;
        
        // 更新用戶銀行帳戶
        $userData = ['bank_account' => $bankAccount];
        $this->userRepository->updateUserDeliveryInfo($userAccount, $userData);
        
        // 更新訂單銀行帳戶
        $orderData = ['bank_account' => $bankAccount];
        $this->orderRepository->updateOrderDeliveryInfo($userAccount, $orderData);
        
        return ['success' => '扣款帳號：' . $bankAccount, 'redirect' => 'pay_show'];
    }

    public function confirmPayment(Request $request)
    {
        if (empty($request->name) || empty($request->bank_account) || empty($request->shop1_addr2)) {
            return ['error' => '資料未填寫完整', 'redirect' => 'pay_show'];
        }

        $userAccount = Auth::user()->account ?? Auth::user()->email;

        // 更新訂單為顯示狀態
        $orderUpdates = ['show' => "1"];
        $order = $this->orderRepository->updateOrderDeliveryInfo($userAccount, $orderUpdates);
        
        // 更新用戶通知
        $this->userRepository->updateUserNotification($userAccount, "您的訂單已送出！");
        
        // 發送確認郵件
        $latestOrder = $this->orderRepository->getLatestOrderByAccount($userAccount);
        $products = $this->parsePurchasedProducts($latestOrder->purchased);
        
        $emailResult = $this->emailService->sendPurchaseConfirmationEmail($userAccount, $products, $latestOrder);
        
        // Handle email sending result
        if (!$emailResult['success']) {
            if (isset($emailResult['is_rate_limit']) && $emailResult['is_rate_limit']) {
                // Rate limit error - order is successful but email failed
                return [
                    'success' => '購買成功，訂單id：' . $latestOrder->id . '。' . $emailResult['message'],
                    'redirect' => 'home'
                ];
            } else {
                // Other email error
                return [
                    'success' => '購買成功，訂單id：' . $latestOrder->id . '。' . $emailResult['message'],
                    'redirect' => 'home'
                ];
            }
        }
        
        return ['success' => '購買成功，訂單id：' . $latestOrder->id . '，確認郵件已發送', 'redirect' => 'home'];
    }
}