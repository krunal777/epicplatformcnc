<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CustomAuthGuard
{
    public function handle($request, Closure $next)
    {
        // Custom logic to handle the AuthGuard behavior
        if (!$request->hasHeader('X-Organisation-Id') || !$request->hasHeader('X-Store-Id')) {
            return response()->json(['error' => 'Organisation Id and Store Id headers are required'], 400);
        }
        if (!Auth::guard('jwt')->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
