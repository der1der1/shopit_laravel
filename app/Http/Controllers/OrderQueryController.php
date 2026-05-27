<?php

namespace App\Http\Controllers;

use App\Repository\ProductRepository;
use App\Service\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderQueryController extends Controller
{
    protected $orderService;

    protected $productRepository;

    public function __construct(OrderService $orderService, ProductRepository $productRepository)
    {
        $this->orderService = $orderService;
        $this->productRepository = $productRepository;
    }

    /**
     * 顯示訂單查詢頁面
     * 已登入：直接列出該會員所有訂單
     * 未登入：顯示單號輸入表單
     */
    public function show(Request $request)
    {
        $marqee = $this->productRepository->getAllMarqee();
        $user = Auth::user();
        $orders = [];

        if (Auth::check()) {
            $orders = $this->orderService->getUserOrdersForPage($user);
        }

        return view('order_list', [
            'user' => $user,
            'marqee' => $marqee,
            'orders' => $orders,
            'queried_order' => null,
            'query_error' => null,
        ]);
    }

    /**
     * 未登入使用者以單號查詢訂單狀態
     */
    public function query(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer|min:1',
        ], [
            'order_id.required' => '請輸入訂單編號',
            'order_id.integer' => '訂單編號必須為數字',
        ]);

        $marqee = $this->productRepository->getAllMarqee();
        $order = $this->orderService->getOrderByIdForGuest($request->order_id);

        return view('order_list', [
            'user' => null,
            'marqee' => $marqee,
            'orders' => [],
            'queried_order' => $order,
            'query_error' => $order ? null : '查無此訂單，請確認訂單編號是否正確。',
        ]);
    }
}
