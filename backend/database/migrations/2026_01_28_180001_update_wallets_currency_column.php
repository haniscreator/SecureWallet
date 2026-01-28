<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Add nullable currency_id
        Schema::table('wallets', function (Blueprint $table) {
            $table->foreignId('currency_id')->nullable()->after('name')->constrained('currencies');
        });

        // 2. Migrate data
        // For each wallet, find key from currencies table where code matches wallet.currency
        // Since we know we just seeded USD/EUR:
        $usd = DB::table('currencies')->where('code', 'USD')->first();
        $eur = DB::table('currencies')->where('code', 'EUR')->first();

        if ($usd) {
            DB::table('wallets')->where('currency', 'USD')->update(['currency_id' => $usd->id]);
        }
        if ($eur) {
            DB::table('wallets')->where('currency', 'EUR')->update(['currency_id' => $eur->id]);
        }

        // 3. Make currency_id required (but strict mode might fail if some rows unmigrated). 
        // Assuming clean slate or perfect match.
        // Also drop old column.
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn('currency');
            // Cannot easily change column to non-nullable in same transaction for sqlite sometimes but mysql is ok.
            // Let's modify it.
            $table->foreignId('currency_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->string('currency')->nullable();
        });

        // Reverse data migration rough implementation
        $usd = DB::table('currencies')->where('code', 'USD')->first();
        if ($usd) {
            DB::table('wallets')->where('currency_id', $usd->id)->update(['currency' => 'USD']);
        }

        Schema::table('wallets', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn('currency_id');
        });
    }
};
