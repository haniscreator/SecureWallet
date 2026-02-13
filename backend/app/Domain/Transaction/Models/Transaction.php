<?php

namespace App\Domain\Transaction\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Wallet\Models\Wallet;
use App\Domain\Wallet\Models\ExternalWallet;
use App\Domain\User\Models\User;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_wallet_id',
        'to_wallet_id',
        'external_wallet_id',
        'transaction_status_id',
        'type', // credit, debit
        'amount',
        'reference',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'created_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function status()
    {
        return $this->belongsTo(TransactionStatus::class, 'transaction_status_id');
    }

    public function externalWallet()
    {
        return $this->belongsTo(ExternalWallet::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }


    public function fromWallet()
    {
        return $this->belongsTo(Wallet::class, 'from_wallet_id');
    }

    public function toWallet()
    {
        return $this->belongsTo(Wallet::class, 'to_wallet_id');
    }
}
