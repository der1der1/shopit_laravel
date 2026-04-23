<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 排除綠界非同步通知路由的 CSRF 驗證（ReturnURL 由綠界伺服器呼叫，無 CSRF token）
        $middleware->validateCsrfTokens(except: [
            'ecpay/return',
            'ecpay/result',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
