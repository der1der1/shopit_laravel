<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\UpdateOrderRequest;
use App\Services\OrderService;
use App\Services\MarqueeService;
use Illuminate\Support\Facades\Auth;

class purchasedCtlr extends Controller
{
    protected $orderService;
    protected $marqueeService;

    public function __construct(
        OrderService $orderService,
        MarqueeService $marqueeService
    ) {
        $this->orderService = $orderService;
        $this->marqueeService = $marqueeService;
    }

    public function pay_show()
    {
        $user = Auth::user();
        $marqee = $this->marqueeService->getAllMarquees();
        
        $orderData = $this->orderService->getLastOrder($user->account);
        if (!$orderData) {
            return redirect()->route('home')->with('error', '找不到訂單');
        }

        return view('pay', [
            'user' => $user,
            'marqee' => $marqee,
            'products' => $orderData['products'],
            'ppl_info' => $orderData['user'],
            'purchased' => $orderData['order']
        ]);
    }

    public function map()
    {
        return view('map');
    }

    public function want(UpdateOrderRequest $request)
    {
        if ($this->orderService->addToCart($request->product_id)) {
            return redirect()->route('home')->with('success', '加入成功');
        }

        return redirect()->route('home')->with('error', '加入失敗');
    }

    public function pay_to_shop(UpdateOrderRequest $request)
    {
        $user = Auth::user();
        
        if ($this->orderService->updateOrderDelivery($user->account, $request->validated())) {
            return redirect()->route('pay_show')->with('success', '超商寄送到' . $request->store);
        }

        return redirect()->route('pay_show')->with('error', '更新失敗');
    }

    public function pay_to_home(UpdateOrderRequest $request)
    {
        $user = Auth::user();
        
        if ($this->orderService->updateOrderDelivery($user->account, $request->validated())) {
            return redirect()->route('pay_show')->with('success', '宅配到：' . $request->address);
        }

        return redirect()->route('pay_show')->with('error', '更新失敗');
    }

    public function pay_name(UpdateOrderRequest $request)
    {
        $user = Auth::user();
        
        if ($this->orderService->updateOrderDelivery($user->account, $request->validated())) {
            return redirect()->route('pay_show')->with('success', '取貨大名：' . $request->name_input);
        }

        return redirect()->route('pay_show')->with('error', '更新失敗');
    }

    public function pay_account(UpdateOrderRequest $request)
    {
        $user = Auth::user();
        
        if ($this->orderService->updateOrderDelivery($user->account, $request->validated())) {
            return redirect()->route('pay_show')->with('success', '扣款帳號：' . $request->account_input);
        }

        return redirect()->route('pay_show')->with('error', '更新失敗');
    }

    public function pay_confirm(UpdateOrderRequest $request)
    {
        $user = Auth::user();
        
        if ($this->orderService->confirmOrder($user->account)) {
            return redirect()->route('home')->with('success', '訂單已送出！');
        }

        return redirect()->route('pay_show')->with('error', '訂單送出失敗');
    }

    public function view_mail()
    {
        $purchased = (object)[
            'name' => 'deniel',
            'account' => 'deniel@gmail',
            'bank_account' => '0191227',
            'shop1_addr2' => '2',
            'to_address' => '台南市善化區',
            'bill' => '7788'
        ];

        $products = [
            [
                'product_name' => '商品名稱',
                'id' => '商品ID',
                'num' => 2,
                'price' => 500,
            ],
            [
                'product_name' => '商品名稱2',
                'id' => '商品ID2',
                'num' => 5,
                'price' => 650,
            ]
        ];

        return view('emails.confirm_buy_mail', compact('products', 'purchased'));
    }
}
