<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'phone',
    ];

    protected $hidden = [
        'remember_token',
    ];

    // Связь с чеками
    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }
}