<?php

namespace App\Services;

use App\Repositories\MailListRepository;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Message;

class EmailService
{
    protected $mailListRepository;

    public function __construct(MailListRepository $mailListRepository)
    {
        $this->mailListRepository = $mailListRepository;
    }

    public function sendVerificationEmail(string $email, string $verificationCode, bool $isResend = false)
    {
        $subject = 'Shopit 註冊驗證信';
        
        $message = $isResend 
            ? '重新發送驗證碼，您的驗證碼為：' . $verificationCode . '；請在7分鐘內回到網站進行驗證。' . PHP_EOL . '注意，若您多次重新發送仍無法成功登入，請聯絡系統管理員'
            : '感謝您註冊本站帳號，您的驗證碼為：' . $verificationCode . '；請在7分鐘內回到網站進行驗證。';

        return $this->sendRawEmail($email, $subject, $message);
    }

    public function sendTestEmail(string $email, bool $withAttachment = false)
    {
        try {
            if ($withAttachment) {
                return Mail::send('emails.test_mail_html', [], function (Message $message) use ($email) {
                    $message->to($email)
                        ->subject('Laravel 測試信件')
                        ->attach(public_path('shopit_update.docx'));
                });
            }

            return $this->sendRawEmail($email, 'Laravel 測試信件', '這是一封來自 Laravel 的測試信件！');
        } catch (\Exception $e) {
            Log::error('發信失敗：' . $e->getMessage());
            throw $e;
        }
    }

    public function sendPurchaseConfirmation(string $to, array $products, array $purchased)
    {
        $bcc = $this->getBccList();

        try {
            return Mail::send(
                'emails.confirm_buy_mail',
                ['products' => $products, 'purchased' => $purchased],
                function (Message $message) use ($to, $bcc) {
                    $message->to($to)
                        ->bcc($bcc)
                        ->subject(config('mail.from.name') . '確認購買通知');
                }
            );
        } catch (\Exception $e) {
            Log::error('購買確認郵件發送失敗：' . $e->getMessage());
            throw $e;
        }
    }

    protected function sendRawEmail(string $to, string $subject, string $content)
    {
        try {
            return Mail::raw($content, function (Message $message) use ($to, $subject) {
                $message->to($to)
                    ->subject($subject);
            });
        } catch (\Exception $e) {
            Log::error('發信失敗：' . $e->getMessage());
            throw $e;
        }
    }

    protected function getBccList(): array
    {
        $bcc = [config('mail.from.address')];
        $adminEmails = $this->mailListRepository->getActiveAdminEmails();
        
        return array_merge($bcc, $adminEmails);
    }
}