<?php

namespace App\Service;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;



class RealHumanService
{
    /* the Cloudflare certification conducts only in real web */
    private function validateTurnstile($token)
    {
        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => env('CLOUDFLARE_TURNSTILE_SECRET_KEY'),
            'response' => $token,
        ]);

        return $response->json()['success'] ?? false;
    }

    public function realHuman(Request $request)
    {
        if (config('app.url') === 'https://desmoco.com.tw') {
            if (!$this->validateTurnstile($request->input('cf-turnstile-response'))) {
                return back()->withErrors(['msg' => '請完成真人驗證']);
            }
        }

    }
}
