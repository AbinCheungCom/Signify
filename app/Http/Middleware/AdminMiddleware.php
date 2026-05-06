<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * 管理员权限中间件
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->is_admin) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            if ($request->user()) {
                // 已登录但非管理员 → 返回首页并提示
                return redirect('/')->with('error', '您没有管理员权限');
            }

            // 未登录 → 跳转登录页
            return redirect()->route('login');
        }

        return $next($request);
    }
}
