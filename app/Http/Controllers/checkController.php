<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\StoreOrderRequest;
use App\Services\CartService;
use App\Services\MarqueeService;
use Illuminate\Support\Facades\Auth;

class checkController extends Controller
{
    protected $cartService;
    protected $marqueeService;

    public function __construct(
        CartService $cartService,
        MarqueeService $marqueeService
    ) {
        $this->cartService = $cartService;
        $this->marqueeService = $marqueeService;
    }

    public function check_show()
    {
        $user = Auth::user();
        $marqee = $this->marqueeService->getAllMarquees();
        $wanted_product = $this->cartService->getWishlistProducts($user->account);

        return view('check', compact('user', 'marqee', 'wanted_product'));
    }

    public function check_store(StoreOrderRequest $request)
    {
        dd($request->all());
        try {
            $validated = $request->validated();

            $this->cartService->createOrder(
                $validated['selected_items'],
                $validated['quantity']
            );

            return redirect()->route('pay_show')->with('success', '儲存成功');
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => $e->getMessage()]);
        }
    }
}
