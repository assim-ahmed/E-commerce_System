<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CartCookieMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // إذا مفيش cookie اسمه cart_cookie
        if (!$request->cookie('cart_cookie')) {
            // أنشئ واحد جديد
            $cartCookie = 'cart_' . uniqid() . '_' . time();
            
            // أضف الـ cookie للـ response
            $response = $next($request);
            
            return $response->cookie(
                'cart_cookie',     // الاسم
                $cartCookie,       // القيمة
                60 * 24 * 365,     // المدة (سنة)
                '/',               // path
                null,              // domain
                false,             // secure (false للـ localhost)
                false,             // httpOnly
                false,             // raw
                'lax'              // sameSite
            );
        }
        
        return $next($request);
    }
}