<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\AdminOrderService;

class listController extends Controller
{
    protected $adminOrderService;

    public function __construct(AdminOrderService $adminOrderService)
    {
        $this->adminOrderService = $adminOrderService;
    }
    public function list_show()
    {
        $result = $this->adminOrderService->getAdminOrderList();
        
        if (isset($result['error'])) {
            return redirect()->route($result['redirect'])->with('error', $result['error']);
        }
        
        return view('list', [
            'user' => $result['user'],
            'marqee' => $result['marqee'],
            'new_lists' => $result['new_lists']
        ]);
    }

    public function list_store(Request $request)
    {
        $result = $this->adminOrderService->processOrderDelivery($request);
        
        return redirect()->route($result['redirect'])->with('success', $result['success']);
    }
}
