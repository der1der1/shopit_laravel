<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailTestController extends Controller
{
    public function send(Request $request)
    {
        // 測試收件者，可寫在 URL 如：/send-test-mail?email=test@example.com
        $to = $request->input('email', 'test@example.com');

        /* 發送信件，in vivo content */
        // Mail::raw('這是一封來自 Laravel 的測試信件！', function ($message) use ($to) {
        //     $message->to($to)
        //             ->subject('Laravel 測試信件');
        // });

        /* 發送信件，in vitro test.blade.php
        resources/views/emails/test_mail_html.blade.php */
        Mail::send('emails.test_mail_html', [], function ($message) use ($to) {
            $message->to($to)
                    ->subject('Laravel 測試信件')
                    
                    /* 附件 */
                    ->attach(public_path('shopit_update.docx'))
                    ;
        });



        return '郵件已發送至：' . $to;
    }
    public function test()
    {
        // 測試用
        $to = 'deniel87deniel87@gmail.com';
        // $to = 'serlina0504@gmail.com';
        // $to = 'deniel@photonic.com.tw';

        try {
            Mail::raw('這是一封來自 deniel Desmoco 的測試信件！', function ($message) use ($to) {
                $message->to($to)
                        ->subject('Laravel 測試信件');
            });

            return '✅ 郵件已發送至：' . $to;
        } catch (Exception $e) {
            Log::error('❌ 發信失敗：' . $e->getMessage());
            return '❌ 發信失敗：' . $e->getMessage();
        }
    }
}
