<?php

namespace App\Console\Commands;

use App\Enums\SubscriptionStatus;
use App\Models\Subscriptions\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Console\Command;

class ResetMonthlySubscriptions extends Command
{
    protected $signature = 'subscriptions:reset-monthly
                            {--dry-run : Muestra cuántas suscripciones se reiniciarían sin aplicar cambios}';

    protected $description = 'Pasa a pago pendiente las suscripciones activas del período vencido — corre el día 1 de cada mes';

    public function handle(SubscriptionService $subscriptionService): int
    {
        if ($this->option('dry-run')) {
            $total = Subscription::query()
                ->where('status', SubscriptionStatus::ACTIVE)
                ->where('current_period_end', '<', now()->startOfDay())
                ->count();

            $this->comment("[DRY-RUN] Se reiniciarían {$total} suscripción(es). Ejecutá sin --dry-run para aplicar.");
            return self::SUCCESS;
        }

        $reset = $subscriptionService->resetMonthly();

        $this->info("Completado: <fg=yellow>{$reset} suscripción(es)</> pasada(s) a pago pendiente.");

        return self::SUCCESS;
    }
}
