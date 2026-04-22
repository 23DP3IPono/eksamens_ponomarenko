<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DienasPunkts extends Model
{
    use HasFactory;

    protected $table = 'dienas_punkts';
    protected $primaryKey = 'punkts_id';

    protected $fillable = [
        'datums',
        'apraksts',
        'celojuma_id',
        'vieta_id',
    ];

    // The trip this day point belongs to
    public function celojums()
    {
        return $this->belongsTo(Celojums::class, 'celojuma_id');
    }

    // The place this day point happens at
    public function vieta()
    {
        return $this->belongsTo(Vieta::class, 'vieta_id');
    }
}