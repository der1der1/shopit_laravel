<?php

namespace App\Http\Controllers;

use App\Http\Requests\Mail\SendMailRequest;
use App\Services\EmailService;
use Illuminate\Support\Facades\Log;

class MailTestController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function send(SendMailRequest $request)
    {
        try {
            $validated = $request->validated();
            
            switch ($validated['type']) {
                case 'test':
                    $this->emailService->sendTestEmail($validated['email']);
                    break;
                
                case 'test_with_attachment':
                    $this->emailService->sendTestEmail($validated['email'], true);
                    break;
                
                case 'purchase_confirmation':
                    $this->emailService->sendPurchaseConfirmation(
                        $validated['email'],
                        $validated['products'],
                        $validated['purchased']
                    );
                    break;
            }

            return '郵件已發送至：' . $validated['email'];
        } catch (\Exception $e) {
            Log::error('發信失敗：' . $e->getMessage());
            return response()->json(['error' => '郵件發送失敗：' . $e->getMessage()], 500);
        }
    }

    public function test()
    {
        try {
            $email = 'deniel87deniel87@gmail.com';
            $this->emailService->sendTestEmail($email);
            
            return '✅ 郵件已發送至：' . $email;
        } catch (\Exception $e) {
            Log::error('❌ 發信失敗：' . $e->getMessage());
            return '❌ 發信失敗：' . $e->getMessage();
        }
    }

    public function buy_confirm_mail($to, array $products, array $purchased)
    {
        try {
            $this->emailService->sendPurchaseConfirmation($to, $products, $purchased);
            return '郵件已發送至：' . $to;
        } catch (\Exception $e) {
            Log::error('購買確認郵件發送失敗：' . $e->getMessage());
            return response()->json(['error' => '郵件發送失敗：' . $e->getMessage()], 500);
        }
    }
}
