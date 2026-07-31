<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Local;
use App\Models\Subscriptions\Plan;
use App\Models\Subscriptions\Subscription;
use App\Models\Subscriptions\SubscriptionPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    public function __construct(
        private readonly MercadoPagoService $mercadoPagoService,
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // Da de alta la suscripción de un local a un plan. Si el local ya tiene
    // una suscripción, la devuelve tal cual (no se puede duplicar por local).
    // ─────────────────────────────────────────────────────────────────────────

    public function subscribe(Local $local, Plan $plan): Subscription
    {
        if ($local->subscription) {
            return $local->subscription;
        }

        return Subscription::create([
            'local_id'              => $local->id,
            'plan_id'               => $plan->id,
            'status'                => SubscriptionStatus::PENDING_PAYMENT,
            'current_period_start'  => Carbon::now()->startOfMonth(),
            'current_period_end'    => Carbon::now()->endOfMonth(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Crea (o reutiliza) el pago pendiente del período en curso y genera la
    // preference de Checkout Pro. Devuelve ['init_point', 'preference_id'].
    // ─────────────────────────────────────────────────────────────────────────

    public function createPaymentIntent(Subscription $subscription): array
    {
        $periodStart = Carbon::now()->startOfMonth();
        $periodEnd   = Carbon::now()->endOfMonth();

        $payment = $subscription->payments()
            ->where('period_start', $periodStart->toDateString())
            ->where('status', PaymentStatus::PENDING)
            ->first();

        if (! $payment) {
            Log::info("Payment intent no encontrado, creando uno nuevo...");
            $payment = $subscription->payments()->create([
                'amount'       => $subscription->plan->price,
                'currency'     => $subscription->plan->currency,
                'status'       => PaymentStatus::PENDING,
                'period_start' => $periodStart,
                'period_end'   => $periodEnd,
            ]);
        }else{
            Log::info("Payment intent encontrado, reutilizando el existente...");   
        }

        Log::info("detalles de Pago", $payment->toArray());
        
        $result = $this->mercadoPagoService->createSubscriptionPreference($payment);

        $payment->update(['preference_id' => $result['preference_id']]);

        return $result;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Corre el día 1 de cada mes: toda suscripción activa cuyo período ya
    // terminó vuelve a quedar en pago pendiente para el nuevo mes.
    // Devuelve la cantidad de suscripciones afectadas.
    // ─────────────────────────────────────────────────────────────────────────

    public function resetMonthly(): int
    {
        $today = Carbon::now()->startOfDay();

        $subscriptions = Subscription::query()
            ->where('status', SubscriptionStatus::ACTIVE)
            ->where('current_period_end', '<', $today)
            ->get();

        foreach ($subscriptions as $subscription) {
            $subscription->update([
                'status'                => SubscriptionStatus::PENDING_PAYMENT,
                'current_period_start'  => $today->copy()->startOfMonth(),
                'current_period_end'    => $today->copy()->endOfMonth(),
            ]);
        }

        return $subscriptions->count();
    }
}
