<?php

namespace App\Domain\Wallet\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domain\User\Models\User;
use App\Domain\Wallet\Models\Transaction;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'currency',
        'status',
    ];

    /**
     * Get the balance derived from transactions.
     */
    public function getBalanceAttribute()
    {
        $credits = $this->incomingTransactions()->sum('amount');
        $debits = $this->outgoingTransactions()->sum('amount');

        // If it's a dual-entry system check:
        // Incoming (to_wallet_id = this) -> Add
        // Outgoing (from_wallet_id = this) -> Subtract

        return $credits - $debits;
    }

    public function incomingTransactions()
    {
        return $this->hasMany(Transaction::class, 'to_wallet_id');
    }

    public function outgoingTransactions()
    {
        return $this->hasMany(Transaction::class, 'from_wallet_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'wallet_user', 'wallet_id', 'user_id')
            ->withTimestamps();
    }
}
