<?php

namespace App\Filament\App\Pages;

use App\Enums\Role;
use App\Models\Subscriptions\Plan;
use App\Models\Subscriptions\Subscription;
use App\Services\SubscriptionService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Suscripcion extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Suscripción';
    protected static ?string $title           = 'Suscripción';
    protected static ?int    $navigationSort   = 0;
    protected static string  $view             = 'filament.app.pages.suscripcion';

    // Siempre visible para el dueño de local: es la única opción accesible
    // cuando el local no tiene una suscripción activa.
    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole(Role::SPA_OWNER) ?? false;
    }

    public function mount(): void
    {
        $status = request()->query('status');

        match ($status) {
            'success' => Notification::make()->success()->title('Pago recibido, confirmando con Mercado Pago...')->send(),
            'failure' => Notification::make()->danger()->title('El pago no se pudo completar')->send(),
            'pending' => Notification::make()->warning()->title('Tu pago está pendiente de confirmación')->send(),
            default   => null,
        };
    }

    public function getSubscription(): ?Subscription
    {
        return auth()->user()?->local?->subscription;
    }

    public function getPlan(): ?Plan
    {
        return Plan::active()->first();
    }

    public function pay(SubscriptionService $subscriptionService): void
    {
        $local = auth()->user()?->local;
        $plan  = $this->getPlan();

        if (! $local || ! $plan) {
            Notification::make()->danger()->title('No se encontró el local o el plan de suscripción')->send();
            return;
        }

        $subscription = $subscriptionService->subscribe($local, $plan);
        $result       = $subscriptionService->createPaymentIntent($subscription);

        $this->redirect($result['init_point']);
    }
}
