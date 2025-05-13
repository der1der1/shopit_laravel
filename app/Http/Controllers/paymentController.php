<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Api\Contact\ContactApiController;
// use App\Models\orderformModel;
use App\Models\ContactModel;

class PaymentController extends Controller
{
    public function paid_complete(Request $request) {
        // $orderform = OrderformModel::where('MerchantTradeNo', $request->MerchantTradeNo);

        // $orderform->MerchantID           = $request->MerchantID;
        // $orderform->StoreID              = $request->StoreID;
        // $orderform->RtnCode              = $request->RtnCode;
        // $orderform->RtnMsg               = $request->RtnMsg;
        // $orderform->TradeNo              = $request->TradeNo;
        // $orderform->TradeAmt             = $request->TradeAmt;
        // $orderform->PaymentDate          = $request->PaymentDate;
        // $orderform->PaymentTypeChargeFee = $request->PaymentTypeChargeFee;
        // $orderform->TradeDate            = $request->TradeDate;
        // $orderform->SimulatePaid         = $request->SimulatePaid;
        // $orderform->CustomField1         = $request->CustomField1;
        // $orderform->CustomField2         = $request->CustomField2;
        // $orderform->CustomField3         = $request->CustomField3;
        // $orderform->CustomField4         = $request->CustomField4;
        // $orderform->CheckMacValue        = $request->CheckMacValue;

        // $orderform->save();

        echo "1|OK";
    }

    public function paid_complete_show(Request $request) {
        $MerchantTradeNo = $request->input('MerchantTradeNo');
        $orderform = OrderformModel::where('MerchantTradeNo', $MerchantTradeNo)->first();
        $contact = ContactModel::where('MerchantTradeNo', $MerchantTradeNo)->first();

        return view('client.paid_complete_show', compact('orderform', 'contact'));
    }

    /* the payment route here */ 
    public function go_to_payment(Request $request) {

        $contactApiController = ContactApiController::instance();

        $insertData = $request->all();
        $insertData['MerchantTradeNo'] = strtoupper(substr(env('APP_NAME', 'Laravel'), 0, 3)) . Str::random(7) . rand(100, 999);
        $request['contactTypeId'] = '2';
        $request['langId'] = '1';

        $contactApiController->addContact($request, $insertData);

        $deposit = 1900;

        include(base_path() . '/app/Services/ECPay.Payment.Integration.php');

        $object = new \ECPay_AllInOne();

        $object->ServiceURL = env('ECPAY_PAYMENT_SERVICE_URL', 'https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5');
        $object->MerchantID = env('ECPAY_MERCHANT_ID', 3002607);
        $object->HashKey = env('ECPAY_HASHKEY', 'pwFHCqoQZGmho4w6');
        $object->HashIV = env('ECPAY_HASHIV', 'EkRm7iFT261dpevs');
        $object->EncryptType = '1';
        
        $object->Send['MerchantTradeNo'] = $insertData['MerchantTradeNo'];
        $object->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');
        $object->Send['PaymentType'] = 'aio';
        $object->Send['TotalAmount'] = $deposit;
        $object->Send['TradeDesc'] = '租車訂金';
        $object->Send['ItemName'] = '租車訂金';
        $object->Send['Items'] = [
            [
                'Name' => '租車訂金',
                'Price' => $deposit,
                'Currency' => "元",
                'Quantity' => 1,
                'URL' => config('app.url')
            ]
        ];
        // 新訂單寫入 oderform table
        OrderformModel::create([
            'product' => '租車訂金',
            'MerchantTradeNo' => $insertData['MerchantTradeNo'],
            'MerchantTradeDate' => date('Y/m/d H:i:s'),
            'TotalAmount' => $deposit,
            'payment' => '3',
            'PaymentType' => 'aio',
        ]);

        $object->Send['ReturnURL'] = config('app.url') . '/paid_complete';  // 綠界回傳付款資訊之POST網址
        $object->Send['ClientBackURL'] = config('app.url') . '/paid_complete_show?' . http_build_query(['MerchantTradeNo' => $insertData['MerchantTradeNo']]);  // 付款成功後的返回頁面
        $object->Send['ChoosePayment'] = \ECPay_PaymentMethod::Credit;

        return $object->CheckOut();
    }
}
