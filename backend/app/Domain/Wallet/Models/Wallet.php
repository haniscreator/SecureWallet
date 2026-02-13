<?php

namespace App\Domain\Wallet\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\WalletFactory;
use App\Domain\User\Models\User;
use App\Domain\Transaction\Models\Transaction;

class Wallet extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return WalletFactory::new();
    }

    protected $fillable = [
        'address',
        'name',
        'currency_id',
        'status',
    ];

    public function isFrozen(): bool
    {
        return $this->status === false;
    }

    public function isActive(): bool
    {
        return $this->status === true;
    }


    protected $casts = [
        'status' => 'boolean',
    ];

    public function currency()
    {
        return $this->belongsTo(\App\Domain\Currency\Models\Currency::class);
    }

    /**
     * Get the balance derived from transactions.
     */
    public function getBalanceAttribute()
    {
        $credits = $this->incomingTransactions()
            ->whereHas('status', function ($query) {
                $query->where('code', 'completed');
            })
            ->sum('amount');

        $debits = $this->outgoingTransactions()
            ->whereHas('status', function ($query) {
                $query->where('code', 'completed');
            })
            ->sum('amount');

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
