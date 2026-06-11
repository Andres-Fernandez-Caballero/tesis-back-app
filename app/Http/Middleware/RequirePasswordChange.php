<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->must_change_password) {
            // Livewire wire calls don't need to be redirected — the page
            // restriction was already enforced on the initial page load.
            if ($request->hasHeader('X-Livewire')) {
                return $next($request);
            }

            // Allow the change-password page and the logout route.
            if ($request->routeIs('filament.app.pages.cambiar-contrasena')
                || str_contains($request->path(), 'logout')) {
                return $next($request);
            }

            return redirect()->route('filament.app.pages.cambiar-contrasena');
        }

        return $next($request);
    }
}
