<?php

namespace App\Service;

use App\Repository\MailRepository;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Exception;

class EmailService
{
    protected $mailRepository;

    public function __construct(MailRepository $mailRepository)
    {
        $this->mailRepository = $mailRepository;
    }

    public function sendVerificationEmail($request, $privilege, $context, $title)
    {
        try {
            $this->mailRepository->sendVerificationEmail($request, $privilege, $context, $title);
            return ['success' => true, 'message' => '驗證郵件已發送'];
        } catch (Exception $e) {
            Log::error('Verification email failed: ' . $e->getMessage());
            return ['success' => false, 'message' => '郵件發送失敗，請稍後再試'];
        }
    }

    public function sendVerificationEmail2($request, $veriCode, $context)
    {
        try {
            $this->mailRepository->sendVerificationEmail2($request, $veriCode, $context);
            return ['success' => true, 'message' => '驗證郵件已重新發送'];
        } catch (Exception $e) {
            Log::error('Verification email resend failed: ' . $e->getMessage());
            return ['success' => false, 'message' => '郵件發送失敗，請稍後再試'];
        }
    }

    public function sendPurchaseConfirmationEmail($to, $products, $purchased)
    {
        try {
            // Get BCC list
            $bccList = $this->mailRepository->getBccEmailList();
            
            // Attempt to send email
            Mail::send(
                'emails.confirm_buy_mail', 
                ['products' => $products, 'purchased' => $purchased], 
                function ($message) use ($to, $bccList) {
                    $message->to($to)
                        ->bcc($bccList)
                        ->subject(config('mail.from.name') . '確認購買通知');
                }
            );

            Log::info('Purchase confirmation email sent successfully to: ' . $to);
            return [
                'success' => true, 
                'message' => '訂單確認郵件已發送'
            ];

        } catch (Exception $e) {
            // Log the specific error
            Log::error('Purchase confirmation email failed: ' . $e->getMessage(), [
                'to' => $to,
                'error_code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ]);

            // Check if it's a rate limiting error
            if ($this->isRateLimitError($e)) {
                return [
                    'success' => false,
                    'message' => '郵件服務暫時超過使用限制，訂單已成功處理，確認郵件將稍後發送',
                    'is_rate_limit' => true
                ];
            }

            // Other email errors
            return [
                'success' => false,
                'message' => '訂單已成功處理，但郵件發送失敗，請聯繫客服確認',
                'is_rate_limit' => false
            ];
        }
    }

    public function queuePurchaseConfirmationEmail($to, $products, $purchased)
    {
        try {
            // Queue the email to avoid rate limiting
            Queue::push(function($job) use ($to, $products, $purchased) {
                $this->sendPurchaseConfirmationEmail($to, $products, $purchased);
                $job->delete();
            });

            return [
                'success' => true,
                'message' => '訂單確認郵件已加入發送佇列'
            ];

        } catch (Exception $e) {
            Log::error('Failed to queue purchase confirmation email: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => '郵件佇列處理失敗'
            ];
        }
    }

    public function sendTestEmail($to, $subject = 'Laravel 測試信件', $content = '這是一封來自 Laravel 的測試信件！')
    {
        try {
            Mail::raw($content, function ($message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            });

            Log::info('Test email sent successfully to: ' . $to);
            return [
                'success' => true,
                'message' => '✅ 郵件已發送至：' . $to
            ];

        } catch (Exception $e) {
            Log::error('Test email failed: ' . $e->getMessage());
            
            if ($this->isRateLimitError($e)) {
                return [
                    'success' => false,
                    'message' => '❌ 郵件服務已達每日發送限制，請明日再試'
                ];
            }

            return [
                'success' => false,
                'message' => '❌ 發信失敗：' . $e->getMessage()
            ];
        }
    }

    private function isRateLimitError(Exception $e)
    {
        $message = $e->getMessage();
        return (
            strpos($message, '550') !== false && 
            (strpos($message, 'Daily user sending limit exceeded') !== false ||
             strpos($message, 'sending limit') !== false ||
             strpos($message, 'rate limit') !== false)
        );
    }

    public function canSendEmail()
    {
        // Simple test to check if we can send email
        // You could implement more sophisticated rate limiting logic here
        return true;
    }
}