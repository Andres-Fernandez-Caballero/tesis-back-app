<?php

namespace App\Models\Users\Traits;

use App\Models\Users\UserData;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasUserData {
    public function user_data(): HasOne
    {
        return $this->hasOne(UserData::class);
    }
}