<?php

namespace App\Domain\Wallet\Models;

use App\Domain\Currency\Models\Currency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'address',
        'name',
        'currency_id',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
