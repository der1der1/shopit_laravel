<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\ListOrderRequest;
use App\Services\PurchasedService;
use App\Services\MarqueeService;
use Illuminate\Support\Facades\Auth;

class listController extends Controller
{
    protected $purchasedService;
    protected $marqueeService;

    public function __construct(
        PurchasedService $purchasedService,
        MarqueeService $marqueeService
    ) {
        $this->purchasedService = $purchasedService;
        $this->marqueeService = $marqueeService;
    }

    public function list_show()
    {
        $user = Auth::user();
        
        if ($user->prvilige !== 'A') {
            return redirect()->route('home')->with('error', '您沒有權限執行此操作');
        }

        $marqee = $this->marqueeService->getAllMarquees();
        $new_lists = $this->purchasedService->getVisibleOrders();
        
        return view('list', compact('user', 'marqee', 'new_lists'));
    }

    public function list_store(ListOrderRequest $request)
    {
        $validated = $request->validated();
        
        $success = $this->purchasedService->completeOrder(
            $validated['id_done'],
            $validated['account_done']
        );

        if (!$success) {
            return back()->withErrors(['msg' => '訂單處理失敗']);
        }

        return redirect()->route('list_show')
            ->with('success', '單號：' . $validated['id_done'] . '  商品已寄出！');
    }
}
