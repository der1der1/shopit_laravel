<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\EmailService;
use App\Repositories\MailListRepository;
use Illuminate\Support\Facades\Mail;
use Mockery;

class EmailServiceTest extends TestCase
{
    protected $emailService;
    protected $mailListRepository;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        
        $this->mailListRepository = Mockery::mock(MailListRepository::class);
        $this->emailService = new EmailService($this->mailListRepository);
    }

    public function test_send_verification_email()
    {
        $email = 'test@example.com';
        $verificationCode = '123456';

        $this->emailService->sendVerificationEmail($email, $verificationCode);

        Mail::assertSent(function ($mail) use ($email, $verificationCode) {
            return $mail->hasTo($email) &&
                   $mail->subject === 'Shopit 註冊驗證信' &&
                   str_contains($mail->rawMessage, $verificationCode);
        });
    }

    public function test_send_resend_verification_email()
    {
        $email = 'test@example.com';
        $verificationCode = '123456';

        $this->emailService->sendVerificationEmail($email, $verificationCode, true);

        Mail::assertSent(function ($mail) use ($email, $verificationCode) {
            return $mail->hasTo($email) &&
                   $mail->subject === 'Shopit 註冊驗證信' &&
                   str_contains($mail->rawMessage, $verificationCode) &&
                   str_contains($mail->rawMessage, '重新發送驗證碼');
        });
    }

    public function test_send_test_email()
    {
        $email = 'test@example.com';
        
        $this->emailService->sendTestEmail($email);

        Mail::assertSent(function ($mail) use ($email) {
            return $mail->hasTo($email) &&
                   $mail->subject === 'Laravel 測試信件' &&
                   str_contains($mail->rawMessage, '這是一封來自 Laravel 的測試信件！');
        });
    }

    public function test_send_test_email_with_attachment()
    {
        $email = 'test@example.com';
        
        $this->emailService->sendTestEmail($email, true);

        Mail::assertSent(function ($mail) use ($email) {
            return $mail->hasTo($email) &&
                   $mail->subject === 'Laravel 測試信件';
        });
    }

    public function test_send_purchase_confirmation()
    {
        $email = 'test@example.com';
        $products = [['id' => 1, 'name' => 'Test Product']];
        $purchased = ['id' => 1, 'total' => 100];
        $adminEmails = ['admin1@example.com', 'admin2@example.com'];
        
        $this->mailListRepository->shouldReceive('getActiveAdminEmails')
            ->once()
            ->andReturn($adminEmails);

        $this->emailService->sendPurchaseConfirmation($email, $products, $purchased);

        Mail::assertSent(function ($mail) use ($email) {
            return $mail->hasTo($email) &&
                   str_contains($mail->subject, '確認購買通知');
        });
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}