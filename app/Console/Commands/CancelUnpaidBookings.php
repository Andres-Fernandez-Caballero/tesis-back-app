<?php

namespace App\Console\Commands;

use App\Models\Therapists\Booking;
use App\Models\Therapists\States\Booking\BookingCancelled;
use App\Models\Therapists\States\Booking\BookingPendingPayment;
use App\Notifications\UserNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CancelUnpaidBookings extends Command
{
    protected $signature = 'bookings:cancel-unpaid
                            {--dry-run : Muestra cuántos turnos se cancelarían sin aplicar cambios}';

    protected $description = 'Cancela reservas en pending_payment que no fueron pagadas en 8 minutos';

    public function handle(): int
    {
        $cutoff = Carbon::now()->subMinutes(8);

        $query = Booking::query()
            ->where('state', BookingPendingPayment::$name)
            ->where('created_at', '<=', $cutoff);

        $total = $query->count();

        if ($total === 0) {
            $this->info('✓ No hay reservas sin pagar vencidas.');
            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->comment("[DRY-RUN] Se cancelarían {$total} reserva(s) sin pagar. Ejecutá sin --dry-run para aplicar.");
            return self::SUCCESS;
        }

        $cancelled = 0;
        $failed    = 0;

        $query->chunkById(50, function ($bookings) use (&$cancelled, &$failed) {
            foreach ($bookings as $booking) {
                try {
                    DB::transaction(function () use ($booking) {
                        $booking->state->transitionTo(BookingCancelled::class);

                        if ($booking->user) {
                            $booking->user->notify(new UserNotification(
                                title: 'Reserva cancelada',
                                body:  'Tu reserva fue cancelada porque el pago de la seña no se completó dentro del tiempo límite.',
                            ));
                        }
                    });
                    $cancelled++;
                } catch (\Throwable $e) {
                    $this->warn("  ✗ Booking #{$booking->id}: {$e->getMessage()}");
                    $failed++;
                }
            }
        });

        $this->info("Completado: <fg=green>{$cancelled} cancelado(s)</>, <fg=yellow>{$failed} con error</>.");

        return self::SUCCESS;
    }
}
