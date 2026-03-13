<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\PaymentService;
use App\Service\CheckoutService;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class purchasedCtlr extends Controller
{
    protected $paymentService;
    protected $checkoutService;

    public function __construct(PaymentService $paymentService, CheckoutService $checkoutService)
    {
        $this->paymentService = $paymentService;
        $this->checkoutService = $checkoutService;
    }
    public function pay_show()
    {
        $data = $this->paymentService->getPaymentPageData();
        $data['purchased']['account'] = $data['purchased']['account'] == null ? $data['user']->email : $data['purchased']['account'];

        return view('pay', [
            'user' => $data['user'],
            'marqee' => $data['marqee'],
            'products' => $data['products'],
            'ppl_info' => $data['ppl_info'],
            'purchased' => $data['purchased']
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
        dd($request->all());
        $user = User::find(Auth::id());
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
        if (!$deliveryType) {
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

        $result = $this->paymentService->confirmPayment($request);
        // 更新使用者的想要清單
        $this->checkoutService->updateUserWantList($user->account, $selected_items);
        
        if (isset($result['error'])) {
            return redirect()->route($result['redirect'])->with('error', $result['error']);
        }
        
        return redirect()->route($result['redirect'])->with('success', $result['success']);
    }

    public function view_mail(Request $request)
    {
        $purchased = new \stdClass();
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
