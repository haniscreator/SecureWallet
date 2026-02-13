<?php

namespace App\Domain\Transaction\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionStatus extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'name',
        'code',
    ];
}
