<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SafeEnhanceTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Adicionar colunas que não existem
            if (!Schema::hasColumn('transactions', 'metadata')) {
                $table->json('metadata')->nullable();
            }
            
            if (!Schema::hasColumn('transactions', 'processed_at')) {
                $table->timestamp('processed_at')->nullable();
            }
            
            if (!Schema::hasColumn('transactions', 'verification_status')) {
                $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            }
            
            if (!Schema::hasColumn('transactions', 'external_id')) {
                $table->string('external_id')->nullable();
            }
            
            if (!Schema::hasColumn('transactions', 'merchant_category')) {
                $table->string('merchant_category')->nullable();
            }
        });
        
        // Adicionar índices que não existem
        $existingIndexes = DB::select("SHOW INDEX FROM transactions WHERE Key_name = 'transactions_status_verification_status_index'");
        if (empty($existingIndexes)) {
            DB::statement('ALTER TABLE transactions ADD INDEX transactions_status_verification_status_index (status, verification_status)');
        }
        
        $existingIndexes = DB::select("SHOW INDEX FROM transactions WHERE Key_name = 'transactions_external_id_index'");
        if (empty($existingIndexes)) {
            DB::statement('ALTER TABLE transactions ADD INDEX transactions_external_id_index (external_id)');
        }
        
        $existingIndexes = DB::select("SHOW INDEX FROM transactions WHERE Key_name = 'transactions_merchant_category_index'");
        if (empty($existingIndexes)) {
            DB::statement('ALTER TABLE transactions ADD INDEX transactions_merchant_category_index (merchant_category)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'metadata')) {
                $table->dropColumn('metadata');
            }
            
            if (Schema::hasColumn('transactions', 'processed_at')) {
                $table->dropColumn('processed_at');
            }
            
            if (Schema::hasColumn('transactions', 'verification_status')) {
                $table->dropColumn('verification_status');
            }
            
            if (Schema::hasColumn('transactions', 'external_id')) {
                $table->dropColumn('external_id');
            }
            
            if (Schema::hasColumn('transactions', 'merchant_category')) {
                $table->dropColumn('merchant_category');
            }
        });
        
        // Remover índices
        $existingIndexes = DB::select("SHOW INDEX FROM transactions WHERE Key_name = 'transactions_status_verification_status_index'");
        if (!empty($existingIndexes)) {
            DB::statement('ALTER TABLE transactions DROP INDEX transactions_status_verification_status_index');
        }
        
        $existingIndexes = DB::select("SHOW INDEX FROM transactions WHERE Key_name = 'transactions_external_id_index'");
        if (!empty($existingIndexes)) {
            DB::statement('ALTER TABLE transactions DROP INDEX transactions_external_id_index');
        }
        
        $existingIndexes = DB::select("SHOW INDEX FROM transactions WHERE Key_name = 'transactions_merchant_category_index'");
        if (!empty($existingIndexes)) {
            DB::statement('ALTER TABLE transactions DROP INDEX transactions_merchant_category_index');
        }
    }
}
