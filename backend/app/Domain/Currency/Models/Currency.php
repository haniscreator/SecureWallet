<?php

namespace App\Domain\Currency\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\CurrencyFactory;

class Currency extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return CurrencyFactory::new();
    }

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
