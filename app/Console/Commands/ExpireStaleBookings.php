<?php

namespace App\Console\Commands;

use App\Models\Therapists\Booking;
use App\Models\Therapists\States\Booking\BookingConfirmed;
use App\Models\Therapists\States\Booking\BookingExpired;
use App\Models\Therapists\States\Booking\BookingPending;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireStaleBookings extends Command
{
    protected $signature = 'bookings:expire
                            {--dry-run : Muestra cuántos turnos se expirarían sin aplicar cambios}';

    protected $description = 'Marca como "expired" todos los turnos pendientes o confirmados cuya fecha ya pasó';

    public function handle(): int
    {
        $yesterday = Carbon::yesterday()->toDateString();

        $query = Booking::query()
            ->whereIn('state', [BookingPending::$name, BookingConfirmed::$name])
            ->where('date', '<=', $yesterday);

        $total = $query->count();

        if ($total === 0) {
            $this->info('✓ No hay turnos vencidos para expirar.');
            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->comment("[DRY-RUN] Se expirarían {$total} turno(s). Ejecutá sin --dry-run para aplicar.");
            return self::SUCCESS;
        }

        $expired = 0;
        $failed  = 0;

        // Procesamos en chunks para no cargar toda la tabla en memoria
        $query->chunkById(100, function ($bookings) use (&$expired, &$failed) {
            foreach ($bookings as $booking) {
                try {
                    DB::transaction(function () use ($booking) {
                        $booking->state->transitionTo(BookingExpired::class);
                    });
                    $expired++;
                } catch (\Throwable $e) {
                    $this->warn("  ✗ Booking #{$booking->id}: {$e->getMessage()}");
                    $failed++;
                }
            }
        });

        $this->info("Completado: <fg=green>{$expired} expirado(s)</>, <fg=yellow>{$failed} con error</>.");

        return self::SUCCESS;
    }
}
