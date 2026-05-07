<?php

namespace App\Http\Controllers;

use App\Service\CheckoutService;
use App\Service\PaymentService;
use Illuminate\Http\Request;

class checkController extends Controller
{
    protected $checkoutService;

    protected $paymentService;

    public function __construct(CheckoutService $checkoutService, PaymentService $paymentService)
    {
        $this->checkoutService = $checkoutService;
        $this->paymentService = $paymentService;
    }

    public function want(Request $request)
    {
        $result = $this->paymentService->addToWishlist($request);

        if ($request->input('buy_now') === '1') {
            return redirect()->route('check_show')->with('success', $result['success']);
        }

        return back()->with('success', $result['success']);
    }

    // 要先驗證是否已經登入
    public function check_show()
    {
        $data = $this->checkoutService->getCheckoutData();

        return view('check', [
            'user' => $data['user'],
            'marqee' => $data['marqee'],
            'wanted_product' => $data['wanted_product'],
        ]);
    }

    public function check_store(Request $request)
    {
        try {
            $result = $this->checkoutService->processCheckout($request);
            session(['selected_items' => $request->selected_items]);

            if (isset($result['error'])) {
                return redirect()->route($result['redirect'])->with('error', $result['error']);
            }

            return back()->with('success', $result['success']);

        } catch (\Exception $e) {
            return back()->withErrors(['msg' => $e->getMessage()]);
        }
    }
}
