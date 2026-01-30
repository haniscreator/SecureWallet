<?php

namespace App\Domain\Transaction\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Wallet\Models\Wallet;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_wallet_id',
        'to_wallet_id',
        'type', // credit, debit
        'amount',
        'reference',
        'created_at',
    ];

    public function fromWallet()
    {
        return $this->belongsTo(Wallet::class, 'from_wallet_id');
    }

    public function toWallet()
    {
        return $this->belongsTo(Wallet::class, 'to_wallet_id');
    }
}
