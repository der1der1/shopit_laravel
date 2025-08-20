<?php

namespace App\Repository;

use Illuminate\Support\Facades\Mail;



class MailRepository
{

    public function sendVerificationEmail($request, $prvilige, $context, $title)
    {
        
        // 如果前綴是 admin./ 要幫他拿掉
        if ($prvilige == "A") {
            $head_away = explode("admin./", $request->account);
            $request->account = $head_away[1];
        }

        $to = $request->account;
        /* 發送信件，in vivo content */
        Mail::raw($context, function ($message) use ($to, $title) {
            $message->to($to)
                ->subject($title);
        });
    }



}
