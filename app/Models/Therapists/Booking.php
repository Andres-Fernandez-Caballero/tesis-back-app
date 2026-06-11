<?php

namespace App\Models\Therapists;

use App\Models\Especialidad;
use App\Models\Local;
use App\Models\Payments\Transaction;
use App\Models\Review;
use App\Models\Therapists\States\Booking\BookingConfirmed;
use App\Models\Therapists\States\Booking\BookingExpired;
use App\Models\Therapists\States\Booking\BookingPending;
use App\Models\Therapists\States\Booking\BookingPendingPayment;
use App\Models\Therapists\States\Booking\BookingState;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\ModelStates\HasStates;

class Booking extends Model
{
    /** @use HasFactory<\Database\Factories\Therapists\BookingFactory> */
    use HasFactory;
    use HasStates;

    protected $casts = [
        'state' => BookingState::class,
    ];

    protected $guarded = [];

    public function therapist(): BelongsTo
    {
        return $this->belongsTo(Therapist::class);
    }

    public function annuncement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class);
    }

    public function local(): BelongsTo
    {
        return $this->belongsTo(Local::class);
    }

    public function especialidad(): BelongsTo
    {
        return $this->belongsTo(Especialidad::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Accessor: devuelve true si la reserva debería estar expirada en tiempo
    // real, aunque el comando artisan todavía no haya corrido.
    // ──────────────────────────────────────────────────────────────────────────

    public function getIsExpiredAttribute(): bool
    {
        if (! ($this->state instanceof BookingPending || $this->state instanceof BookingConfirmed)) {
            return false;
        }

        return Carbon::parse($this->date)->isPast()
            && ! Carbon::parse($this->date)->isToday();
    }

    // Scope de conveniencia: incluye pending_payment para bloquear slots disponibles
    public function scopeActive($query)
    {
        return $query
            ->whereState('state', [BookingPendingPayment::class, BookingPending::class, BookingConfirmed::class])
            ->where('date', '>=', Carbon::today()->toDateString());
    }
}
