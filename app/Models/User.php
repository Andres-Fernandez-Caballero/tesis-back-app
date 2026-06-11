<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Local;
use App\Models\Users\States\AbstractUserState;
use App\Models\Users\Traits\HasScore;
use App\Models\Users\Traits\HasTherapist;
use App\Models\Users\Traits\HasUserData;
use App\Models\Users\Traits\HasUserFilamentConfig;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\ModelStates\HasStates;
use Spatie\Permission\Traits\HasRoles;
use App\Enums\Role;
use App\Models\Users\Traits\HasNotifications;

class User extends Authenticatable implements FilamentUser, HasName
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;
    use HasRoles;
    use HasUserFilamentConfig;
    use HasStates;
    use HasScore;
    use HasUserData;
    use HasTherapist;
    use HasNotifications;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'must_change_password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'    => 'datetime',
            'password'             => 'hashed',
            'state'                => AbstractUserState::class,
            'banned_to'            => 'datetime',
            'must_change_password' => 'boolean',
        ];
    }


    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn(string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->hasRole([Role::ADMIN]);
    }

    public function local(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Local::class);
    }
}
