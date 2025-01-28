<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Verifica si el usuario tiene el campo two_factor_verified como true
        $user = Auth::user();

        // Si el usuario no ha completado el 2FA, redirige a la página de 2FA
        if (!$user->two_factor_verified) {
            return redirect()->route('login.view');
        }

        return $next($request);
    }
}
