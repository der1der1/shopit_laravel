<?php

namespace App\Repository;

use Illuminate\Support\Facades\Mail;



class MailRepository
{

    public function sendVerificationEmail($request, $prvilige, $veri_code)
    {
        
        // 如果前綴是 admin./ 要幫他拿掉
        if ($prvilige == "A") {
            $head_away = explode("admin./", $request->account);
            $request->account = $head_away[1];
        }

        $to = $request->account;
        /* 發送信件，in vivo content */
        Mail::raw('感謝您註冊本站帳號，您的驗證碼為：' . $veri_code . '；請在7分鐘內回到網站進行驗證。', function ($message) use ($to) {
            $message->to($to)
                ->subject('Shopit 註冊驗證信');
        });
    }



}
