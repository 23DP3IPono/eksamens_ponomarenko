<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vieta extends Model
{
    use HasFactory;

    protected $table = 'vieta';
    protected $primaryKey = 'vieta_id';

    protected $fillable = [
        'nosaukums',
        'adrese',
        'koordinatas',
        'tips',
    ];

    // Day points that happen at this place
    public function dienasPunkti()
    {
        return $this->hasMany(DienasPunkts::class, 'vieta_id');
    }
}