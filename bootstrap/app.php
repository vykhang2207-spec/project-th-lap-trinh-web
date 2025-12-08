<?php

// 1. DÒNG USE CẦN THIẾT ĐỂ LARAVEL TÌM THẤY MIDDLEWARE
use App\Http\Middleware\CheckUserRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // 2. ĐĂNG KÝ ALIAS CHO MIDDLEWARE
        $middleware->alias([
            'role' => CheckUserRole::class, // <-- Bỏ '\App\Http\Middleware\' vì đã 'use' ở trên
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // ... (Phần xử lý exception giữ nguyên)
    })->create();
