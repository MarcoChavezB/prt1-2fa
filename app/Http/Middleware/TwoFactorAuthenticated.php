<?php

namespace App\Http\Middleware;

use App\Http\Controllers\TwoFactorController;
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
        $twoFactorController = new TwoFactorController();

        if(!$user){
            return $next($request);
        }

        // Si el usuario no ha completado el 2FA, redirige a la página de 2FA
        if (!$user->two_factor_verified || now()->gt($user->two_factor_expires_at)) {
            Auth::logout();
            $twoFactorController->deleteTwoFaSession($user);
            return redirect()->route('login.view')->with('error', 'la session expiro, inicia sesion de nuevo');
        }

        return $next($request);
    }
}
