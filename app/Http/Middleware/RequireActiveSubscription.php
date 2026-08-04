<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireActiveSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Un local suspendido ya es redirigido por RequireActiveLocal; no lo
        // desviemos también hacia la página de suscripción.
        if ($user && $user->hasRole(Role::SPA_OWNER) && ($user->local?->isSuspended() ?? false)) {
            return $next($request);
        }

        if ($user && $user->hasRole(Role::SPA_OWNER) &&  ! ($user->local?->hasActiveSubscription() ?? false)) {
            // Livewire wire calls don't need to be redirected — la restricción de
            // página ya se aplicó en la carga inicial (canAccess/canViewAny).
            if ($request->hasHeader('X-Livewire')) {
                return $next($request);
            }

            
            // Permitir la página de Suscripción, cambio de contraseña y el logout.
            if ($request->routeIs('filament.app.pages.suscripcion')
                || $request->routeIs('filament.app.pages.cambiar-contrasena')
                || str_contains($request->path(), 'logout')) {
                return $next($request);
            }

            return redirect()->route('filament.app.pages.suscripcion');
        }

        return $next($request);
    }
}
