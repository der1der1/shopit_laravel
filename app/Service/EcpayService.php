<?php

namespace App\Service;

/**
 * EcpayService
 *
 * 封裝綠界 AIO 金流 — 一般信用卡跳轉付款。
 * 全部 HTTP 通訊以 PHP stream_context 自行實作，不依賴 Guzzle / cURL 擴充。
 */
class EcpayService
{
    private string $merchantId;

    private string $hashKey;

    private string $hashIV;

    private string $apiUrl;

    private string $returnUrl;

    private string $orderResultUrl;

    public function __construct()
    {
        $this->merchantId = config('ecpay.merchant_id');
        $this->hashKey = config('ecpay.hash_key');
        $this->hashIV = config('ecpay.hash_iv');
        $this->apiUrl = config('ecpay.api_url');
        $this->returnUrl = config('ecpay.return_url');
        $this->orderResultUrl = config('ecpay.order_result_url');
    }

    // -----------------------------------------------------------------------
    // 對外主要方法
    // -----------------------------------------------------------------------

    /**
     * 組建付款參數陣列（含 CheckMacValue）
     *
     * @param  array  $orderData  ['trade_no', 'total', 'desc', 'item_name']
     */
    public function buildPaymentParams(array $orderData): array
    {
        $params = [
            'MerchantID' => $this->merchantId,
            'MerchantTradeNo' => $orderData['trade_no'],
            'MerchantTradeDate' => date('Y/m/d H:i:s'),
            'PaymentType' => 'aio',
            'TotalAmount' => (int) $orderData['total'],
            'TradeDesc' => $orderData['desc'] ?? '線上購物',
            'ItemName' => $this->truncateItemName($orderData['item_name'] ?? '商品購買'),
            'ReturnURL' => $this->returnUrl,
            'OrderResultURL' => $this->orderResultUrl,
            'ChoosePayment' => 'Credit',   // 信用卡
            'EncryptType' => 1,          // SHA256
        ];

        $params['CheckMacValue'] = $this->generateCheckMacValue($params);

        return $params;
    }

    /**
     * 建立自動送出的 HTML Form（跳轉至綠界頁面）
     *
     * @param  array  $params  buildPaymentParams() 回傳的陣列
     * @return string 完整 HTML 字串，可直接 echo 或放進 Blade
     */
    public function buildPaymentForm(array $params): string
    {
        // 特殊字元跳脫
        $action = htmlspecialchars($this->apiUrl, ENT_QUOTES);  // 參數代表單引號與雙引號都一起跳脫（預設只跳脫雙引號）
        $html = '<form id="ecpay-form" method="POST" action="'.$action.'">'.PHP_EOL;

        foreach ($params as $key => $value) {
            $html .= '  <input type="hidden" name="'
                .htmlspecialchars($key, ENT_QUOTES)
                .'" value="'
                .htmlspecialchars((string) $value, ENT_QUOTES)
                .'">'.PHP_EOL;
        }

        $html .= '</form>'.PHP_EOL;
        $html .= '<script>document.getElementById("ecpay-form").submit();</script>'.PHP_EOL;

        return $html;
    }

    /**
     * 驗證綠界 callback 回傳的 CheckMacValue
     *
     * @param  array  $data  $_POST 全部欄位
     */
    public function verifyCheckMacValue(array $data): bool
    {
        $received = $data['CheckMacValue'] ?? '';
        unset($data['CheckMacValue']);

        $expected = $this->generateCheckMacValue($data);

        return strtoupper($received) === strtoupper($expected);
    }

    /**
     * 自封裝 HTTP POST（使用 PHP stream context，不需要 Guzzle / ext-curl）
     *
     * @param  array  $data  表單欄位
     * @return string 回應內容
     *
     * @throws \RuntimeException 若請求失敗
     */
    public function httpPost(string $url, array $data): string
    {
        $payload = http_build_query($data);

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", [
                    'Content-Type: application/x-www-form-urlencoded',
                    'Content-Length: '.strlen($payload),
                    'User-Agent: ShopiT/1.0',
                ]),
                'content' => $payload,
                'timeout' => 30,
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            $err = error_get_last();
            throw new \RuntimeException('ECPay HTTP 請求失敗：'.($err['message'] ?? '未知錯誤'));
        }

        return $response;
    }

    // -----------------------------------------------------------------------
    // 內部輔助方法
    // -----------------------------------------------------------------------

    /**
     * 產生 CheckMacValue（SHA256，依綠界規範）
     */
    private function generateCheckMacValue(array $params): string
    {
        // 1. 依參數名稱字典序排列
        ksort($params);

        // 2. 組成 HashKey=...&k=v&...&HashIV=... 字串
        $str = 'HashKey='.$this->hashKey;
        foreach ($params as $k => $v) {
            $str .= '&'.$k.'='.$v;
        }
        $str .= '&HashIV='.$this->hashIV;

        // 3. URL encode 後轉小寫
        $str = strtolower(urlencode($str));

        // 4. 還原綠界指定的不編碼字元
        $keep = [
            '%2d' => '-', '%5f' => '_', '%2e' => '.',
            '%21' => '!', '%2a' => '*', '%28' => '(', '%29' => ')',
        ];
        $str = str_replace(array_keys($keep), array_values($keep), $str);

        // 5. SHA256 後轉大寫
        return strtoupper(hash('sha256', $str));
    }

    /**
     * 截斷 ItemName，確保不超過綠界 400 字元上限
     */
    private function truncateItemName(string $name): string
    {
        if (mb_strlen($name) <= 400) {
            return $name;
        }

        return mb_substr($name, 0, 397).'...';
    }
}
