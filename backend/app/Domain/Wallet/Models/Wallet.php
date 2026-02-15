<?php

namespace App\Domain\Wallet\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\WalletFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domain\User\Models\User;
use App\Domain\Transaction\Models\Transaction;
use App\Domain\Transaction\Models\TransactionStatus;
use App\Domain\Currency\Models\Currency;

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
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the balance derived from transactions.
     */
    public function getBalanceAttribute()
    {
        // Optimization: Use pre-calculated value from service if available
        if ($this->getAttribute('dashboard_balance') !== null) {
            return $this->getAttribute('dashboard_balance');
        }

        $credits = $this->incomingTransactions()
            ->where('transaction_status_id', TransactionStatus::getId(TransactionStatus::CODE_COMPLETED))
            ->sum('amount');

        $debits = $this->outgoingTransactions()
            ->where('transaction_status_id', TransactionStatus::getId(TransactionStatus::CODE_COMPLETED))
            ->sum('amount');

        return $credits - $debits;
    }

    public function getAvailableBalanceAttribute()
    {
        // Optimization: Use pre-calculated value from service if available
        if ($this->getAttribute('dashboard_available_balance') !== null) {
            return $this->getAttribute('dashboard_available_balance');
        }

        $pendingDebit = $this->outgoingTransactions()
            ->where('transaction_status_id', TransactionStatus::getId(TransactionStatus::CODE_PENDING))
            ->sum('amount');

        return $this->balance - $pendingDebit;
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
