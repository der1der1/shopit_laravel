<?php

return [
    /*
    |--------------------------------------------------------------------------
    | 綠界 ECPay 設定
    |--------------------------------------------------------------------------
    | merchant_id   : 特店編號
    | hash_key      : 金鑰 (HashKey)
    | hash_iv       : 向量 (HashIV)
    | api_url       : AIO 金流 API 位址 (測試/正式)
    | return_url    : 綠界伺服器非同步通知 URL (ReturnURL) — 後端接收
    | order_result_url : 付款完成後瀏覽器跳轉 URL (OrderResultURL)
    |--------------------------------------------------------------------------
    */

    'merchant_id' => env('ECPAY_MERCHANT_ID', '2000132'),
    'hash_key' => env('ECPAY_HASH_KEY', '5294y06JbISpM5x9'),
    'hash_iv' => env('ECPAY_HASH_IV', 'v77hoKGq4kWxNNIS'),

    // 測試環境 URL；正式環境改為 https://payment.ecpay.com.tw/Charge/AioCheckOut/V5
    'api_url' => env('ECPAY_API_URL', 'https://payment-stage.ecpay.com.tw/Charge/AioCheckOut/V5'),

    'return_url' => env('ECPAY_RETURN_URL', 'https://yourdomain.com/ecpay/return'),
    'order_result_url' => env('ECPAY_ORDER_RESULT_URL', 'https://yourdomain.com/ecpay/result'),
];
