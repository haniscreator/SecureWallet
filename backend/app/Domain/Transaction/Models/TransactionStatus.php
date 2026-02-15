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

    public const CODE_PENDING = 'pending';
    public const CODE_COMPLETED = 'completed';
    public const CODE_REJECTED = 'rejected';
    public const CODE_CANCELLED = 'cancelled';

    private static array $cache = [];

    public static function getId(string $code): int
    {
        if (!isset(self::$cache[$code])) {
            self::$cache[$code] = self::where('code', $code)->value('id');
        }
        return self::$cache[$code];
    }

    public static function flushCache(): void
    {
        self::$cache = [];
    }
}
