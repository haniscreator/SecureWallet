<?php

namespace App\Domain\Transaction\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\TransactionStatusFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionStatus extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return TransactionStatusFactory::new();
    }

    public $timestamps = true;

    protected $fillable = [
        'name',
        'code',
    ];
}
