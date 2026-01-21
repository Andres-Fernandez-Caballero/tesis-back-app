<?php

namespace App\Models\Therapists;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Therapist extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $hidden = ['field_m', 'field_o'];

    public function newFromBuilder($attributes = [], $connection = null)
    {
        $attributes = (array) $attributes;

        if (isset($attributes['type'])) {
            $class = '\\App\\Models\\' . $attributes['type'];

            if (class_exists($class)) {
                $instance = (new $class)->newInstance([], true);
                $instance->setRawAttributes($attributes, true);
                $instance->setConnection($connection ?: $this->getConnectionName());
                return $instance;
            }
        }

        return parent::newFromBuilder($attributes, $connection);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    public function availabilyExceptions(): HasMany
    {
        return $this->hasMany(AvailabilityException::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
