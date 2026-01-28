<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Users: Add status column (assuming it didn't exist)
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('status')->default(true)->after('role'); // 1 = active, 0 = inactive
        });

        // 2. Wallets: Update status column
        // First, we need to handle existing data conversion if we want to be safe.
        // In SQLite/MySQL for this dev stage, we might just drop and add, but let's try to map.
        // 'active' -> 1, 'frozen' -> 0.

        // Since we can't easily change type with mapping in one go without raw queries or temp columns,
        // and assuming this is dev environment, we'll use a temp column approach for safety or just raw updates if possible.
        // Simpler approach for "Tech Assignment":
        // Drop old, add new with default. Data migration:

        DB::table('wallets')->where('status', 'active')->update(['status' => '1']);
        DB::table('wallets')->where('status', 'frozen')->update(['status' => '0']);

        // Now change the type.
        Schema::table('wallets', function (Blueprint $table) {
            $table->boolean('status')->default(true)->change();
        });

        // 3. Currencies: Update status column
        // 'active' -> 1, 'inactive' -> 0
        DB::table('currencies')->where('status', 'active')->update(['status' => '1']);
        DB::table('currencies')->where('status', 'inactive')->update(['status' => '0']);

        Schema::table('currencies', function (Blueprint $table) {
            $table->boolean('status')->default(true)->change();
        });
    }

    public function down(): void
    {
        // Reverse operations

        // 1. Users
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // 2. Wallets
        Schema::table('wallets', function (Blueprint $table) {
            $table->string('status')->default('active')->change();
        });
        // Restore data roughly
        DB::table('wallets')->where('status', '1')->update(['status' => 'active']);
        DB::table('wallets')->where('status', '0')->update(['status' => 'frozen']);

        // 3. Currencies
        Schema::table('currencies', function (Blueprint $table) {
            $table->string('status')->default('active')->change();
        });
        DB::table('currencies')->where('status', '1')->update(['status' => 'active']);
        DB::table('currencies')->where('status', '0')->update(['status' => 'inactive']);
    }
};
