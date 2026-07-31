<?php

namespace App\Models\Subscriptions;

use App\Enums\SubscriptionStatus;
use App\Models\Local;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    protected $fillable = [
        'local_id',
        'plan_id',
        'status',
        'current_period_start',
        'current_period_end',
    ];

    protected $casts = [
        'status'                => SubscriptionStatus::class,
        'current_period_start'  => 'date',
        'current_period_end'    => 'date',
    ];

    public function local(): BelongsTo
    {
        return $this->belongsTo(Local::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SubscriptionPayment::class);
    }

    public function isActive(): bool
    {
        return $this->status === SubscriptionStatus::ACTIVE;
    }

    public function isPendingPayment(): bool
    {
        return $this->status === SubscriptionStatus::PENDING_PAYMENT;
    }
}
