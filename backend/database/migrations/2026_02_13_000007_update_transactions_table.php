<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Drop existing status column if exists (it was string 'pending' etc in some versions or we are replacing logic)
            // The original migration had: $table->string('type'); // credit, debit. It did NOT have status.
            // Wait, let me check 2026_01_28_170001_create_transactions_table.php content again.
            // It had: type, amount, reference. No status.

            $table->foreignId('transaction_status_id')->nullable()->after('type')->constrained('transaction_statuses');
            $table->foreignId('external_wallet_id')->nullable()->after('to_wallet_id')->constrained('external_wallets');
            $table->text('rejection_reason')->nullable()->after('reference');
            $table->foreignId('approved_by')->nullable()->after('rejection_reason')->constrained('users');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['transaction_status_id']);
            $table->dropColumn('transaction_status_id');

            $table->dropForeign(['external_wallet_id']);
            $table->dropColumn('external_wallet_id');

            $table->dropColumn('rejection_reason');

            $table->dropForeign(['approved_by']);
            $table->dropColumn('approved_by');

            $table->dropColumn('approved_at');
        });
    }
};
