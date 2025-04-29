<?php

namespace App\Models\Users;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Score extends Model
{
    /** @use HasFactory<\Database\Factories\Users\ScoreFactory> */
    use HasFactory;

    protected $guarded = [];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
