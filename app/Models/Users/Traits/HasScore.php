<?php

namespace App\Models\Users\Traits;

use App\Models\Users\Score;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasScore
{
    protected function _scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }

    public function getScoreAttribute(): int
    {
        $score = $this->_scores()->sum('starts')/$this->_scores()->count() ?? 0;
        return intval($score);
    }

    public function getScoreCountAttribute(): int
    {
        return $this->_scores()->count();
    }

    public function getLastScoreAttribute():Score|null
    {
        return $this->_scores()->latest()->first();
    }
}