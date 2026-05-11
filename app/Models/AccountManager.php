<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountManager extends Model
{
    protected $fillable = ['name', 'nik', 'email', 'phone', 'telegram_chat_id', 'last_reminded_at'];

    protected $casts = [
        'last_reminded_at' => 'datetime',
    ];

    public function instansi(): HasMany
    {
        return $this->hasMany(Instansi::class);
    }
}
