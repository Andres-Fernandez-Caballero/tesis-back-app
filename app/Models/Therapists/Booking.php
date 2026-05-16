<?php

namespace App\Models\Therapists;

use App\Models\Payments\Transaction;
use App\Models\Therapists\States\Booking\BookingState;
use App\Models\User;
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
}
