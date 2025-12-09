<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     * @param string $role Danh sách role cho phép, ngăn cách bởi dấu | (ví dụ: 'admin|teacher')
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Chưa đăng nhập -> đá về trang Login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Tách chuỗi role thành mảng (Hỗ trợ cú pháp "teacher|admin")
        // Ví dụ: "teacher|admin" -> ['teacher', 'admin']
        $allowedRoles = explode('|', $role);

        // 3. Kiểm tra: Role của user hiện tại có nằm trong danh sách cho phép không?
        if (!in_array(Auth::user()->role, $allowedRoles)) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        return $next($request);
    }
}
