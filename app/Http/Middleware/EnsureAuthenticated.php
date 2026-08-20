<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && !session()->has('user')) {
            $user = Auth::user();
            if ($user) {
                $user->loadMissing('division');
                session(['user' => $user->toArray()]);
            }
        }

        if (!Auth::check() && !session()->has('user')) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Sesi telah berakhir, silakan login kembali.'], 401);
            }
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return $next($request);
    }
}
