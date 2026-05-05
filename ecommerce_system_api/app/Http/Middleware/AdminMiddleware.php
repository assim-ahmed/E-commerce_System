<?php
// app/Http/Middleware/AdminMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // نتأكد إن فيه مستخدم مسجل دخول
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Unauthenticated',
                'errors' => null
            ], 401);
        }

        // نتأكد إن المستخدم عنده صلاحية admin
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Unauthorized. Admin access required.',
                'errors' => null
            ], 403);
        }

        return $next($request);
    }
}