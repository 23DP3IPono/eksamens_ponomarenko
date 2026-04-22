<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Izdevums extends Model
{
    use HasFactory;

    protected $table = 'izdevums';
    protected $primaryKey = 'izdevums_id';

    protected $fillable = [
        'summa',
        'datums',
        'kategorija',
        'celojuma_id',
    ];

    public function celojums()
    {
        return $this->belongsTo(Celojums::class, 'celojuma_id');
    }
}