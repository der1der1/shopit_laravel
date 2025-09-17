<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Service\CheckoutService;

class checkController extends Controller
{
    protected $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }
    // 要先驗證是否已經登入
    public function check_show()
    {
        $data = $this->checkoutService->getCheckoutData();
        
        return view('check', [
            'user' => $data['user'],
            'marqee' => $data['marqee'],
            'wanted_product' => $data['wanted_product']
        ]);
    }
    public function check_store(Request $request)
    {
        try {
            $result = $this->checkoutService->processCheckout($request);
            
            if (isset($result['error'])) {
                return redirect()->route($result['redirect'])->with('error', $result['error']);
            }
            
            return redirect()->route($result['redirect'])->with('success', $result['success']);
            
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => $e->getMessage()]);
        }
    }
    
}
