<?php

namespace App\Http\Controllers;

use App\Repository\OrderRepository;
use App\Service\EcpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * EcpayController
 *
 * 處理綠界非同步通知（ReturnURL）以及付款結果跳轉（OrderResultURL）。
 */
class EcpayController extends Controller
{
    protected EcpayService $ecpayService;

    protected OrderRepository $orderRepository;

    public function __construct(EcpayService $ecpayService, OrderRepository $orderRepository)
    {
        $this->ecpayService = $ecpayService;
        $this->orderRepository = $orderRepository;
    }

    // -----------------------------------------------------------------------
    // ReturnURL：綠界伺服器非同步通知（Server to Server POST）
    // 必須回傳純文字 "1|OK"，否則綠界會重複通知
    // -----------------------------------------------------------------------
    public function returnNotify(Request $request)
    {
        $data = $request->all();

        Log::info('[ECPay] ReturnURL callback', $data);

        // 1. 驗證 CheckMacValue
        if (! $this->ecpayService->verifyCheckMacValue($data)) {
            Log::warning('[ECPay] CheckMacValue 驗證失敗', $data);

            return response('0|CheckMacValue Error', 200)->header('Content-Type', 'text/plain');
        }

        // 2. 解析訂單 ID（MerchantTradeNo 格式：SHP{8碼order_id}{9碼timestamp}）
        $tradeNo = $data['MerchantTradeNo'] ?? '';
        $orderId = (int) substr($tradeNo, 3, 8);

        if (! $orderId) {
            Log::warning('[ECPay] 無法解析 MerchantTradeNo', ['trade_no' => $tradeNo]);

            return response('0|Order Not Found', 200)->header('Content-Type', 'text/plain');
        }

        // 3. 依付款結果更新訂單
        $rtnCode = (int) ($data['RtnCode'] ?? 0);

        if ($rtnCode === 1) {
            // 付款成功
            $this->orderRepository->updateOrderStatus($orderId, ['payed' => '1']);
            Log::info('[ECPay] 訂單付款成功', ['order_id' => $orderId]);
        } else {
            // 付款失敗
            Log::warning('[ECPay] 付款失敗', [
                'order_id' => $orderId,
                'RtnCode' => $rtnCode,
                'RtnMsg' => $data['RtnMsg'] ?? '',
            ]);
        }

        // 4. 固定回傳 "1|OK"（不論成功失敗，只要驗簽通過即回 OK）
        return response('1|OK', 200)->header('Content-Type', 'text/plain');
    }

    // -----------------------------------------------------------------------
    // OrderResultURL：付款完成後瀏覽器跳轉（GET 或 POST）
    // -----------------------------------------------------------------------
    public function orderResult(Request $request)
    {
        $data = $request->all();
        $rtnCode = (int) ($data['RtnCode'] ?? 0);
        $rtnMsg = $data['RtnMsg'] ?? '';

        $success = ($rtnCode === 1);

        // 解析訂單 ID（MerchantTradeNo 格式：SHP{8碼order_id}{9碼timestamp}）
        $tradeNo = $data['MerchantTradeNo'] ?? '';
        $orderId = (int) substr($tradeNo, 3, 8);

        Log::info('[ECPay] OrderResultURL callback', [
            'order_id' => $orderId,
            'RtnCode' => $rtnCode,
            'RtnMsg' => $rtnMsg,
        ]);

        if ($orderId) {
            if ($success) {
                // 付款成功 — 補寫 payed（ReturnURL server callback 可能尚未到達時的 fallback）
                $this->orderRepository->updateOrderStatus($orderId, ['payed' => '1']);
            } else {
                // 付款失敗 — 記錄失敗狀態
                $this->orderRepository->updateOrderStatus($orderId, ['payed' => '0']);
                Log::warning('[ECPay] 瀏覽器端回報付款失敗', [
                    'order_id' => $orderId,
                    'RtnCode' => $rtnCode,
                    'RtnMsg' => $rtnMsg,
                ]);
            }
        } else {
            Log::warning('[ECPay] OrderResultURL 無法解析 MerchantTradeNo', ['trade_no' => $tradeNo]);
        }

        return view('ecpay_result', compact('success', 'rtnCode', 'rtnMsg', 'data', 'orderId'));
    }
}
