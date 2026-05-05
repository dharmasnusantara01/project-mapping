<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountManager extends Model
{
    protected $fillable = ['name', 'nik', 'email', 'phone'];

    public function instansi(): HasMany
    {
        return $this->hasMany(Instansi::class);
    }
}
