<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireActiveLocal
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Alcanza tanto al dueño del local como a los masajistas colaboradores.
        $isLocalMember = $user && ($user->hasRole(Role::SPA_OWNER) || $user->hasRole(Role::MASSAGE_THERAPIST));

        if ($isLocalMember && ($user->associatedLocal()?->isSuspended() ?? false)) {
            // Livewire wire calls don't need to be redirected — la restricción de
            // página ya se aplicó en la carga inicial (canAccess/canViewAny).
            if ($request->hasHeader('X-Livewire')) {
                return $next($request);
            }

            // Permitir solo la página de cuenta suspendida y el logout.
            if ($request->routeIs('filament.app.pages.cuenta-suspendida')
                || str_contains($request->path(), 'logout')) {
                return $next($request);
            }

            return redirect()->route('filament.app.pages.cuenta-suspendida');
        }

        return $next($request);
    }
}
