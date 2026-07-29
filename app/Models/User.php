<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * A login account. Demographic details live on Patient, not here --
 * a User row only answers "who is signing in and what may they do".
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /** Only set for accounts with role = patient. */
    public function patient(): HasOne
    {
        return $this->hasOne(Patient::class);
    }

    public function isMidwife(): bool
    {
        return $this->role === UserRole::Midwife;
    }

    public function isHealthWorker(): bool
    {
        return $this->role === UserRole::HealthWorker;
    }

    public function isPatient(): bool
    {
        return $this->role === UserRole::Patient;
    }

    public function isStaff(): bool
    {
        return $this->role->isStaff();
    }
}
