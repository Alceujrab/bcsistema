<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class EnhanceTransactionsTableSafe2 extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Verificar e adicionar colunas que não existem
            if (!Schema::hasColumn('transactions', 'status')) {
                $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending')->after('type');
            }
            
            if (!Schema::hasColumn('transactions', 'processed_at')) {
                $table->timestamp('processed_at')->nullable()->after('updated_at');
            }
            
            if (!Schema::hasColumn('transactions', 'metadata')) {
                $table->json('metadata')->nullable()->after('notes');
            }
        });

        // Verificar e criar índices que não existem
        $existingIndexes = $this->getExistingIndexes();
        
        Schema::table('transactions', function (Blueprint $table) use ($existingIndexes) {
            // Só adicionar índices que não existem
            if (!in_array('transactions_amount_type_index', $existingIndexes)) {
                $table->index(['amount', 'type'], 'transactions_amount_type_index');
            }
            
            if (!in_array('transactions_status_processed_at_index', $existingIndexes)) {
                $table->index(['status', 'processed_at'], 'transactions_status_processed_at_index');
            }
            
            if (!in_array('transactions_created_at_index', $existingIndexes)) {
                $table->index('created_at', 'transactions_created_at_index');
            }
        });

        // Atualizar dados existentes se necessário
        DB::table('transactions')
            ->whereNull('status')
            ->orWhere('status', '')
            ->update(['status' => 'pending']);
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Remover índices criados (verificar se existem primeiro)
            $existingIndexes = $this->getExistingIndexes();
            
            if (in_array('transactions_amount_type_index', $existingIndexes)) {
                $table->dropIndex('transactions_amount_type_index');
            }
            
            if (in_array('transactions_status_processed_at_index', $existingIndexes)) {
                $table->dropIndex('transactions_status_processed_at_index');
            }
            
            if (in_array('transactions_created_at_index', $existingIndexes)) {
                $table->dropIndex('transactions_created_at_index');
            }
            
            // Remover colunas adicionadas (verificar se existem primeiro)
            if (Schema::hasColumn('transactions', 'metadata')) {
                $table->dropColumn('metadata');
            }
            
            if (Schema::hasColumn('transactions', 'processed_at')) {
                $table->dropColumn('processed_at');
            }
            
            // Não remover status pois pode estar sendo usada
        });
    }

    private function getExistingIndexes()
    {
        $indexes = DB::select("SHOW INDEX FROM transactions");
        return collect($indexes)->pluck('Key_name')->toArray();
    }
}
