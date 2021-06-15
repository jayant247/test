<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable  implements JWTSubject
{
    use HasApiTokens;
    use HasFactory;
    //use HasProfilePhoto;
    use Notifiable, HasRoles;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile_no'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // protected $appends = [
    //     'profile_photo_url',
    // ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /*public function role()
    {
        return $this->belongsToMany(Role::class);
    }*/

    public function role()
    {
        return $this->belongsTo('App\Model\Role');
    }

    public function transactions()
    {
        return $this->hasMany(UserWallet::class);
    }
    public function validTransactions()
    {
        return $this->transactions()->where('status', 1);
    }
    public function credit()
    {
        return $this->validTransactions()
            ->where('type', 'credit')
            ->sum('amount');
    }
    public function debit()
    {
        return $this->validTransactions()
            ->where('type', 'debit')
            ->sum('amount');
    }
    public function balance()
    {
        return $this->credit() - $this->debit();
    }
    public function allowWithdraw($amount) : bool
    {
        return $this->balance() >= $amount;
    }
}
