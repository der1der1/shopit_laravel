<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\PaymentService;

class purchasedCtlr extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }
    public function pay_show()
    {
        $data = $this->paymentService->getPaymentPageData();
        
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

    public function want(Request $request)
    {
        $result = $this->paymentService->addToWishlist($request);
        
        return redirect()->route($result['redirect'])->with('success', $result['success']);
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
        $result = $this->paymentService->confirmPayment($request);
        
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
