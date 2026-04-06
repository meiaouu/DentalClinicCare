<?php

namespace App\Models;

use App\Enums\RoleName;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'role_id',
        'first_name',
        'middle_name',
        'last_name',
        'sex',
        'birth_date',
        'contact_number',
        'email',
        'username',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'last_login_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function getAuthPassword()
    {
        return $this->password_hash;
    }
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function patient(): HasOne
    {
        return $this->hasOne(Patient::class, 'user_id', 'user_id');
    }

    public function dentist(): HasOne
    {
        return $this->hasOne(Dentist::class, 'user_id', 'user_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim(collect([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])->filter()->implode(' '));
    }

    public function hasRole(string|RoleName $role): bool
    {
        $value = $role instanceof RoleName ? $role->value : $role;

        return optional($this->role)->role_name === $value;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(RoleName::ADMIN);
    }

    public function isStaff(): bool
    {
        return $this->hasRole(RoleName::STAFF);
    }

    public function isDentist(): bool
    {
        return $this->hasRole(RoleName::DENTIST);
    }

    public function isPatient(): bool
    {
        return $this->hasRole(RoleName::PATIENT);
    }
}
