<?php

namespace App\Service;

use App\Models\User;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentService
{
    protected $orderRepository;

    protected $productRepository;

    protected $userRepository;

    protected $emailService;

    protected $ecpayService;

    public function __construct(
        OrderRepository $orderRepository,
        ProductRepository $productRepository,
        UserRepository $userRepository,
        EmailService $emailService,
        EcpayService $ecpayService
    ) {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
        $this->emailService = $emailService;
        $this->ecpayService = $ecpayService;
    }

    public function getPaymentPageData()
    {
        $user = Auth::user();
        $marqee = $this->productRepository->getAllMarqee();

        // 已登入使用者使用 account，來賓使用 session 中暫存的帳號識別字串
        $account = $user ? ($user->account ?? $user->email) : session('guest_account');

        if (! $account) {
            return ['error' => '找不到結帳資料，請重新加入購物車', 'redirect' => 'check_show'];
        }

        $userInfo = $user ? User::where('account', $user->account ?? $user->email)->first() : null;
        $latestOrder = $this->orderRepository->getLatestOrderByAccount($account);

        if (! $latestOrder) {
            return ['error' => '找不到訂單，請重新結帳', 'redirect' => 'check_show'];
        }

        // 資料層說明: 以商品-品項作前台顯示，以品項儲存want；到pay頁之前整理在此，直接將商品與品項合併，資訊以品項為主，只是多掛一個key存商品名
        $products = $this->parsePurchasedProducts($latestOrder->purchased);
        // 剔除 $products 中 null 與重複項
        $products = array_filter($products); // 移除 null 項
        $products = array_unique($products, SORT_REGULAR); // 移除重複項

        unset($latestOrder->purchased);  // 已經整理在products

        // 更新價格合計
        $finalBill = 0;
        foreach ($products as $product) {
            $finalBill += $product['num'] * $product['price'];
        }
        $latestOrder->bill = $finalBill;

        return [
            'user' => $user,
            'marqee' => $marqee,
            'products' => $products,
            'ppl_info' => $userInfo,
            'purchased' => $latestOrder,
        ];
    }

    private function parsePurchasedProducts($purchased)
    {
        $purchaseItems = explode(';', $purchased);
        $products = [];

        foreach ($purchaseItems as $purchase) {
            $purchaseData = explode(',', $purchase);
            $variant = $this->productRepository->findVariantById($purchaseData[0]);
            $product = $this->productRepository->findProductById($variant->product_id);

            if ($product) {
                $products[] = [
                    'id' => $variant->id,  // 注意 此處id是品項
                    'num' => $purchaseData[1],
                    'pic_dir' => $variant->pic_dir,
                    'product_name' => $product->product_name,
                    'variant_name' => $variant->variant_name,
                    'description' => $product->description,
                    'price' => $variant->use_oriprice == '1' ? $variant->ori_price : $variant->price,
                ];
            }
        }

        return $products;
    }

    public function addToWishlist(Request $request)
    {
        // 儲存品項ID與數量的組合字串，格式：variantId-quantity
        $prod_vari = $request->variant_id.'-'.$request->quantity;

        if (Auth::check()) {
            // 已登入：儲存至資料庫
            $userAccount = Auth::user()->account;
            $this->userRepository->addToWishlist($userAccount, $prod_vari);
        } else {
            // 未登入來賓：儲存至 session（格式與 DB want 欄位相同，逗號分隔）
            $currentCart = session('guest_cart', '');
            session(['guest_cart' => $currentCart.$prod_vari.',']);
        }

        return ['success' => '加入成功', 'redirect' => 'home'];
    }

    public function updateDeliveryToStore(Request $request)
    {
        // 已登入使用者使用 account，來賓使用 session 帳號識別字串
        $userAccount = Auth::check() ? Auth::user()->account : session('guest_account');
        $storeName = $request->store;

        // 更新訂單信息
        $orderData = [
            'to_shop' => $storeName,
            'shop1_addr2' => '1',
        ];
        $this->orderRepository->updateOrderDeliveryInfo($userAccount, $orderData);

        // 更新用戶信息（只有已登入才有對應的 User 紀錄）
        if (Auth::check()) {
            $userData = [
                'to_shop' => $storeName,
            ];
            $this->userRepository->updateUserDeliveryInfo($userAccount, $userData);
        }

        return ['success' => '超商寄送到'.$storeName, 'redirect' => 'pay_show'];
    }

    public function updateDeliveryToHome(Request $request)
    {
        if (empty($request->address)) {
            return ['error' => '住家地址不可空白', 'redirect' => 'pay_show'];
        }

        // 已登入使用者使用 account，來賓使用 session 帳號識別字串
        $userAccount = Auth::check() ? Auth::user()->account : session('guest_account');
        $address = $request->address;

        // 更新訂單信息
        $orderData = [
            'to_address' => $address,
            'shop1_addr2' => '2',
        ];
        $this->orderRepository->updateOrderDeliveryInfo($userAccount, $orderData);

        // 更新用戶信息（只有已登入才有對應的 User 紀錄）
        if (Auth::check()) {
            $userData = [
                'to_address' => $address,
            ];
            $this->userRepository->updateUserDeliveryInfo($userAccount, $userData);
        }

        return ['success' => '宅配到：'.$address, 'redirect' => 'pay_show'];
    }

    public function updateRecipientName(Request $request)
    {
        if (empty($request->name_input)) {
            return ['error' => '請輸入姓名', 'redirect' => 'pay_show'];
        }

        // 已登入使用者使用 account，來賓使用 session 帳號識別字串
        $userAccount = Auth::check() ? Auth::user()->account : session('guest_account');
        $name = $request->name_input;

        // 更新用戶名稱（只有已登入才有對應的 User 紀錄）
        if (Auth::check()) {
            $userData = ['name' => $name];
            $this->userRepository->updateUserDeliveryInfo($userAccount, $userData);
        }

        // 更新訂單名稱
        $orderData = ['name' => $name];
        $this->orderRepository->updateOrderDeliveryInfo($userAccount, $orderData);

        return ['success' => '取貨大名：'.$name, 'redirect' => 'pay_show'];
    }

    public function updateBankAccount(Request $request)
    {
        if (empty($request->account_input)) {
            return ['error' => '請輸入正確的扣款帳號', 'redirect' => 'pay_show'];
        }

        // 已登入使用者使用 account，來賓使用 session 帳號識別字串
        $userAccount = Auth::check() ? Auth::user()->account : session('guest_account');
        $bankAccount = $request->account_input;

        // 更新用戶銀行帳戶（只有已登入才有對應的 User 紀錄）
        if (Auth::check()) {
            $userData = ['bank_account' => $bankAccount];
            $this->userRepository->updateUserDeliveryInfo($userAccount, $userData);
        }

        // 更新訂單銀行帳戶
        $orderData = ['bank_account' => $bankAccount];
        $this->orderRepository->updateOrderDeliveryInfo($userAccount, $orderData);

        return ['success' => '扣款帳號：'.$bankAccount, 'redirect' => 'pay_show'];
    }

    public function processPayment(Request $request)
    {
        // 已登入使用者使用 account，來賓使用 session 帳號識別字串
        $userAccount = Auth::check() ? (Auth::user()->account ?? Auth::user()->email) : session('guest_account');
        $order = $this->orderRepository->getLatestOrderByAccount($userAccount);

        // 組建 MerchantTradeNo：SHP + 8碼訂單ID + 9碼時間戳尾碼，共 20 字元
        $tradeNo = 'SHP'
            .str_pad((string) $order->id, 8, '0', STR_PAD_LEFT)
            .substr((string) time(), -9);

        // 組建 ItemName（綠界以 # 分隔，上限 400 字元）
        $products = $this->parsePurchasedProducts($order->purchased);
        $itemNames = array_map(
            fn ($p) => $p['product_name'].' x'.$p['num'],
            $products
        );
        $itemName = implode('#', $itemNames) ?: '商品購買';

        // 建立綠界付款參數並產生自動送出的 HTML Form
        $params = $this->ecpayService->buildPaymentParams([
            'trade_no' => $tradeNo,
            'total' => (int) $order->bill,
            'desc' => '線上購物',
            'item_name' => $itemName,
        ]);

        $form = $this->ecpayService->buildPaymentForm($params);

        return ['form' => $form, 'order' => $order, 'trade_no' => $tradeNo];
    }

    public function confirmPayment(Request $request)
    {
        if (empty($request->name) || empty($request->bank_account) || empty($request->shop1_addr2)) {
            return ['error' => '資料未填寫完整', 'redirect' => 'pay_show'];
        }

        // 已登入使用者使用 account，來賓使用 session 帳號識別字串
        $userAccount = Auth::check() ? (Auth::user()->account ?? Auth::user()->email) : session('guest_account');

        // 更新訂單為顯示狀態
        $orderUpdates = ['show' => '1'];
        $order = $this->orderRepository->updateOrderDeliveryInfo($userAccount, $orderUpdates);

        // 更新用戶通知（只有已登入才有對應的 User 紀錄）
        if (Auth::check()) {
            $this->userRepository->updateUserNotification($userAccount, '您的訂單已送出！');
        }

        // 發送確認郵件
        $latestOrder = $this->orderRepository->getLatestOrderByAccount($userAccount);
        $products = $this->parsePurchasedProducts($latestOrder->purchased);

        $emailResult = $this->emailService->sendPurchaseConfirmationEmail($userAccount, $products, $latestOrder);

        // Handle email sending result
        if (! $emailResult['success']) {
            if (isset($emailResult['is_rate_limit']) && $emailResult['is_rate_limit']) {
                // Rate limit error - order is successful but email failed
                return [
                    'success' => '購買成功，訂單id：'.$latestOrder->id.'。'.$emailResult['message'],
                    'redirect' => 'home',
                ];
            } else {
                // Other email error
                return [
                    'success' => '購買成功，訂單id：'.$latestOrder->id.'。'.$emailResult['message'],
                    'redirect' => 'home',
                ];
            }
        }

        return ['success' => '購買成功，訂單id：'.$latestOrder->id.'，確認郵件已發送', 'redirect' => 'home'];
    }
}
