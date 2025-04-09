<?php

namespace App\Models\Therapists;

use Illuminate\Database\Eloquent\Model;

class Therapist extends Model
{
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
}
