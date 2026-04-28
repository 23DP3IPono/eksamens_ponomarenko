<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'uzvards',
        'email',
        'password',
        'loma',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function celojumi()
    {
        return $this->hasMany(Celojums::class, 'lietotajs_id');
    }
public function favoriteCelojumi()
{
    return $this->belongsToMany(
        Celojums::class,
        'favorites',
        'user_id',
        'celojuma_id',
        'id',
        'celojuma_id'
    )->withTimestamps();
}

}