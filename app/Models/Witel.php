<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Witel extends Model
{
    protected $table = 'witel';

    protected $fillable = ['name', 'code'];

    public function instansi(): HasMany
    {
        return $this->hasMany(Instansi::class);
    }
}
