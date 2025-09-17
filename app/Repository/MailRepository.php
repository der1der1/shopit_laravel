<?php

namespace App\Repository;

use Illuminate\Support\Facades\Mail;
use App\Models\mailListModel;

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

    public function sendVerificationEmail2($request, $veri_code, $context)
    {
        $to = $request->email;
        Mail::raw($context, function ($message) use ($to) {
            $message->to($to)
                ->subject('Shopit 註冊驗證信');
        });
    }

    public function getActiveAdminEmails()
    {
        return mailListModel::where('onoff', 1)
            ->where('id', '!=', 1)
            ->pluck('email')
            ->toArray();
    }

    public function getBccEmailList()
    {
        $bcc = [];
        $bcc[] = config('mail.from.address');
        
        $adminEmails = $this->getActiveAdminEmails();
        if (!empty($adminEmails)) {
            foreach ($adminEmails as $adminEmail) {
                $bcc[] = $adminEmail;
            }
        }
        
        return $bcc;
    }
}
