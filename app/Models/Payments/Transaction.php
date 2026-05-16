<?php

namespace App\Models\Payments;

use App\Enums\TransactionStatus;
use App\Models\Therapists\Booking;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'client_id',
        'therapist_id',
        'amount',
        'currency',
        'status',
        'description',
    ];

    protected $casts = [
        'status' => TransactionStatus::class,
        'total_amount' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
    public function therapist()
    {
        return $this->belongsTo(User::class, 'therapist_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function hasApprovedPayment(): bool
    {
        return $this->payments()
            ->where('payment_status', TransactionStatus::COMPLETED)
            ->exists();
    }

    public function lastPayment()
    {
        return $this->payments()->latest()->first();
    }
}
