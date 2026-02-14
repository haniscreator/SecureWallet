<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->string('address')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            // We can't easily revert to NOT NULL without ensuring data integrity, 
            // but for this exercise attempt to revert if possible or just leave nullable.
            // Ideally we'd fill nulls before reverting.
            // For now, simpler to just attempt change back or do nothing.
            // $table->string('address')->nullable(false)->change();
        });
    }
};
