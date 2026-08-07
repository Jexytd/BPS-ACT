<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('user')) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Sesi telah berakhir, silakan login kembali.'], 401);
            }
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return $next($request);
    }
}
