<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            // Adicionar novo campo bank_code
            $table->string('bank_code')->nullable()->after('agency');
        });
        
        // Atualizar o enum type para incluir investment
        // Para MySQL/MariaDB
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE bank_accounts MODIFY COLUMN type ENUM('checking', 'savings', 'investment', 'credit_card')");
        } else {
            // Para SQLite, precisamos recriar a tabela temporariamente
            Schema::table('bank_accounts', function (Blueprint $table) {
                $table->string('type_new')->after('type');
            });
            
            DB::statement('UPDATE bank_accounts SET type_new = type');
            
            Schema::table('bank_accounts', function (Blueprint $table) {
                $table->dropColumn('type');
            });
            
            Schema::table('bank_accounts', function (Blueprint $table) {
                $table->renameColumn('type_new', 'type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            // Remover campo bank_code
            $table->dropColumn('bank_code');
        });
        
        // Reverter o enum type apenas no MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE bank_accounts MODIFY COLUMN type ENUM('checking', 'savings', 'credit_card')");
        }
    }
};
