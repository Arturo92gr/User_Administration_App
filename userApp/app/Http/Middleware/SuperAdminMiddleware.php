<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if ($user == null || ($user->id !== 1 && $user->role !== 'superadmin')) {
            return redirect()->route('index'); // Sin mensaje de error para no dar pistas
        }
        /* if ($user->id !== 1) {
            return redirect()->route('home');
        } */
        return $next($request);
    }
}
