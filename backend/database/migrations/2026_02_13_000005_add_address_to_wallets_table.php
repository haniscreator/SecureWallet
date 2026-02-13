<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Domain\Wallet\Models\Wallet;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->uuid('address')->nullable()->after('id');
        });

        // Generate addresses for existing wallets
        Wallet::chunk(100, function ($wallets) {
            foreach ($wallets as $wallet) {
                // We use UUID for address as requested (unique, uuid)
                $wallet->update(['address' => (string) Str::uuid()]);
            }
        });

        Schema::table('wallets', function (Blueprint $table) {
            $table->uuid('address')->nullable(false)->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn('address');
        });
    }
};
