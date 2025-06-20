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
        Schema::table('account_receivables', function (Blueprint $table) {
            // Alterar a coluna category de enum para string
            $table->string('category', 100)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_receivables', function (Blueprint $table) {
            // Reverter para enum (pode não funcionar com dados existentes)
            $table->enum('category', ['sales', 'services', 'rent', 'other'])->default('other')->change();
        });
    }
};
