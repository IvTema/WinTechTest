<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->bigInteger('amount')->change();
        });

        Schema::table('balances', function (Blueprint $table) {
            $table->bigInteger('usd')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->float('amount')->change();
        });
        Schema::table('balances', function (Blueprint $table) {
            $table->float('usd')->change();
        });
    }
};
