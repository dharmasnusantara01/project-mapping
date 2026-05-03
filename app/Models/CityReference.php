<?php

namespace App\Models;

use Database\Factories\CityReferenceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CityReference extends Model
{
    /** @use HasFactory<CityReferenceFactory> */
    use HasFactory;

    protected $fillable = ['name', 'province', 'latitude', 'longitude'];

    protected $casts = [
        'latitude'  => 'decimal:7',
        'longitude' => 'decimal:7',
    ];
}
