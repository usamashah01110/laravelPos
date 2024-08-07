<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $prefix)
    {
        $user = auth()->user();
        if ($user->isAdmin() && ( $prefix === 'admin' || $prefix === 'users')) {
            return $next($request);
        } elseif ($user->isUser() && $prefix === 'users') {
            return $next($request);
        }
        elseif ($user->isOwner() && $prefix === 'users') {
            return $next($request);
        }
        elseif ($user->isStaff() && $prefix === 'users') {
            return $next($request);
        }else {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }
    }


}
