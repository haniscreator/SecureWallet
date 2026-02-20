<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // USD, EUR
            $table->string('name');
            $table->string('symbol')->nullable();
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
