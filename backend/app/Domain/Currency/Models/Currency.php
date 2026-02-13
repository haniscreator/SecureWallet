<?php

namespace App\Domain\Currency\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\CurrencyFactory;
use Illuminate\Database\Eloquent\Model;

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
