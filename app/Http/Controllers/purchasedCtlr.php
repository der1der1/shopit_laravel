<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repository\OrderRepository;
use App\Service\CheckoutService;
use App\Service\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class purchasedCtlr extends Controller
{
    protected $paymentService;

    protected $checkoutService;

    protected $orderRepository;

    public function __construct(PaymentService $paymentService, CheckoutService $checkoutService, OrderRepository $orderRepository)
    {
        $this->paymentService = $paymentService;
        $this->checkoutService = $checkoutService;
        $this->orderRepository = $orderRepository;
    }

    public function pay_show()
    {
        $data = $this->paymentService->getPaymentPageData();

        // 若 Service 回傳錯誤（如找不到訂單），重導向至購物車
        if (isset($data['error'])) {
            return redirect()->route($data['redirect'])->with('error', $data['error']);
        }

        // 已登入時才設定 account 欄位（來賓無對應 User 紀錄）
        if (Auth::check() && isset($data['purchased']->account)) {
            $data['purchased']['account'] = $data['purchased']['account'] == null ? $data['user']->email : $data['purchased']['account'];
        }

        return view('pay', [
            'user' => $data['user'],
            'marqee' => $data['marqee'],
            'products' => $data['products'],
            'ppl_info' => $data['ppl_info'],
            'purchased' => $data['purchased'],
        ]);
    }

    public function map()
    {
        return view('map');
    }

    public function pay_to_shop(Request $request)
    {
        $result = $this->paymentService->updateDeliveryToStore($request);

        return redirect()->route($result['redirect'])->with('success', $result['success']);
    }

    public function pay_to_home(Request $request)
    {
        $result = $this->paymentService->updateDeliveryToHome($request);

        if (isset($result['error'])) {
            return redirect()->route($result['redirect'])->with('error', $result['error']);
        }

        return redirect()->route($result['redirect'])->with('success', $result['success']);
    }

    public function pay_name(Request $request)
    {
        $result = $this->paymentService->updateRecipientName($request);

        if (isset($result['error'])) {
            return redirect()->route($result['redirect'])->with('error', $result['error']);
        }

        return redirect()->route($result['redirect'])->with('success', $result['success']);
    }

    public function pay_account(Request $request)
    {
        $result = $this->paymentService->updateBankAccount($request);

        if (isset($result['error'])) {
            return redirect()->route($result['redirect'])->with('error', $result['error']);
        }

        return redirect()->route($result['redirect'])->with('success', $result['success']);
    }

    public function pay_confirm(Request $request)
    {
        $user = Auth::user();
        $selected_items = session()->get('selected_items');

        // 整合所有欄位的驗證與資料處理
        $validated = $request->validate([
            'name_input' => 'required|string|max:50',
            'account_input' => 'required|string|max:50',
            // 配送方式相關欄位
            // 若有 store 則必須選擇門市，否則必須有 address
        ]);

        $deliveryType = null;
        if ($request->has('store') && $request->input('store')) {
            $deliveryType = 'store';
        } elseif ($request->has('address') && $request->input('address')) {
            $deliveryType = 'home';
        }
        if (! $deliveryType) {
            return back()->with('error', '請選擇配送方式與填寫相關資訊')->withInput();
        }

        // 整合配送資訊
        if ($deliveryType === 'store') {
            $request->merge([
                'shop1_addr2' => '1',
                'to_shop' => $request->input('store'),
                'to_address' => null,
            ]);
        } else {
            $request->merge([
                'shop1_addr2' => '2',
                'to_shop' => null,
                'to_address' => $request->input('address'),
            ]);
        }
        $request->merge([
            'name' => $request->input('name_input'),
            'bank_account' => $request->input('account_input'),
        ]);

        // 先確認訂單（儲存、發送確認信）
        $result = $this->paymentService->confirmPayment($request);

        if (isset($result['error'])) {
            return redirect()->route($result['redirect'])->with('error', $result['error']);
        }

        // 產生綠界跳轉付款表單並顯示過渡頁（必須在清除 session 之前，來賓需要 guest_account 查詢訂單）
        $paymentResult = $this->paymentService->processPayment($request);

        if (Auth::check() && $user) {
            // 已登入：更新使用者的想要清單（移除已購買的商品）
            $this->checkoutService->updateUserWantList($user->account, $selected_items);
        } else {
            // 來賓：確認付款流程完成後才清除 session
            session()->forget('guest_cart');
            session()->forget('guest_account');
        }

        // 本地開發環境：略過綠界，直接模擬付款成功
        if (app()->environment('local')) {
            $order = $paymentResult['order'];
            $orderId = $order->id;
            $this->orderRepository->updateOrderStatus($orderId, ['payed' => '1']);

            $success = true;
            $rtnCode = 1;
            $rtnMsg = '模擬付款成功（本地環境）';
            $data = ['MerchantTradeNo' => $paymentResult['trade_no']];

            return view('ecpay_result', compact('success', 'rtnCode', 'rtnMsg', 'data', 'orderId'));
        }

        return view('ecpay_redirect', ['ecpayForm' => $paymentResult['form']]);
    }

    public function view_mail(Request $request)
    {
        $purchased = new \stdClass;
        $purchased->name         = 'deniel';
        $purchased->account      = 'deniel@gmail';
        $purchased->bank_account = '0191227';
        $purchased->shop1_addr2  = '2';
        $purchased->to_address   = '台南市善化區';
        $purchased->bill         = '7788';
        $products = [
            [
                'product_name' => '商品名稱',
                'id'           => '商品ID',
                'num'          => 2,
                'price'        => 500,
            ],

            [
                'product_name' => '商品名稱2',
                'id'           => '商品ID2',
                'num'          => 5,
                'price'        => 650,
            ]
        ];

        return view('emails.confirm_buy_mail', compact('request', 'products', 'purchased'));
    }
}
