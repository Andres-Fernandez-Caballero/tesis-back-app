<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Limpieza de datos existentes:
 * Marca como 'expired' todos los turnos cuyo estado sea 'pending' o 'confirmed'
 * y cuya fecha ya haya pasado.
 *
 * No se borran registros; sólo se actualiza el estado.
 */
return new class extends Migration
{
    public function up(): void
    {
        $yesterday = now()->subDay()->toDateString();

        DB::table('bookings')
            ->whereIn('state', ['pending', 'confirmed'])
            ->where('date', '<=', $yesterday)
            ->update([
                'state'      => 'expired',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // No es posible determinar con certeza qué estado tenía cada fila antes
        // de la migración, por lo que el rollback sólo registra un warning.
        // Si necesitás revertir manualmente, restaurá desde un backup.
    }
};
