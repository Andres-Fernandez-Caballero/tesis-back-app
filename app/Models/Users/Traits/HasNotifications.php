<?php

namespace App\Models\Users\Traits;

use App\Models\NotificationToken;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

trait HasNotifications
{
    use Notifiable;
     
    public function notificationTokens(): HasMany
    {
        return $this->hasMany(NotificationToken::class);
    }
}