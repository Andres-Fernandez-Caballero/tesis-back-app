<?php

namespace App\Models\Subscriptions;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPayment extends Model
{
    protected $fillable = [
        'subscription_id',
        'amount',
        'currency',
        'status',
        'external_id',
        'preference_id',
        'payment_data',
        'period_start',
        'period_end',
        'paid_at',
    ];

    protected $casts = [
        'payment_data' => 'array',
        'amount'       => 'decimal:2',
        'period_start' => 'date',
        'period_end'   => 'date',
        'paid_at'      => 'datetime',
        'status'       => PaymentStatus::class,
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
