<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;


class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
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
    'last_login_at',
];

    protected $hidden = [
    'password',
    'remember_token',
];


    public function role()
{
    return $this->belongsTo(Role::class, 'role_id', 'role_id');
}

    public function patient(): HasOne
    {
        return $this->hasOne(Patient::class, 'user_id', 'user_id');
    }
    public function dentist()
{
    return $this->hasOne(\App\Models\Dentist::class, 'user_id', 'user_id');
}


}
