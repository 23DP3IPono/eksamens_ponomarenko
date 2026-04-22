<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rezervacija extends Model
{
    use HasFactory;

    protected $table = 'rezervacija';
    protected $primaryKey = 'rezerv_num';

    protected $fillable = [
        'tips',
        'pakalpojuma_nosaukums',
        'cena',
        'celojuma_id',
    ];

    public function celojums()
    {
        return $this->belongsTo(Celojums::class, 'celojuma_id');
    }
}