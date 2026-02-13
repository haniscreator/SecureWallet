<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('external_wallets', function (Blueprint $table) {
            $table->id();
            $table->string('address')->unique();
            $table->string('name');
            $table->foreignId('currency_id')->constrained('currencies');
            $table->boolean('status')->default(true); // 1 = active, 0 = inactive
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('external_wallets');
    }
};
