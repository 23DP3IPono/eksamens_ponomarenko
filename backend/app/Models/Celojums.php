<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Celojums extends Model
{
    use HasFactory;

    protected $table = 'celojums';
    protected $primaryKey = 'celojuma_id';

    protected $fillable = [
        'nosaukums',
        'galamerkis',
        'valsts',
        'sakuma_datums',
        'beigu_datums',
        'budzets',
        'lietotajs_id',
    ];

    // User
    public function lietotajs()
    {
        return $this->belongsTo(User::class, 'lietotajs_id');
    }

    // Day points
    public function dienasPunkti()
    {
        return $this->hasMany(DienasPunkts::class, 'celojuma_id');
    }

    // Reservations
    public function rezervacijas()
    {
        return $this->hasMany(Rezervacija::class, 'celojuma_id');
    }

    // Expenses
    public function izdevumi()
    {
        return $this->hasMany(Izdevums::class, 'celojuma_id');
    }

    // Users who favorited this trip
public function favoritedBy()
{
    return $this->belongsToMany(
        User::class,
        'favorites',
        'celojuma_id',
        'user_id',
        'celojuma_id',
        'id'
    )->withTimestamps();
}

}