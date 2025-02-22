<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CodeVerify
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
        $user = Auth::user();

        // Verifica si el usuario está autenticado, su correo electrónico está verificado
        // o si ya ha completado el proceso de 2FA
        if ($user && ($user->email_verified_at || $user->two_factor_verified)) {
            // Redirige al usuario a una página principal o a donde corresponda
            return redirect()->route('home'); // Aquí puedes cambiar la ruta de redirección a tu preferencia
        }

        return $next($request);
    }
}
