<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class EnhanceTransactionsTableSafe extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Verificar e adicionar colunas uma por vez para evitar conflitos
        $this->addColumnIfNotExists('transactions', 'category_id', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable();
        });
        
        $this->addColumnIfNotExists('transactions', 'tags', function (Blueprint $table) {
            $table->text('tags')->nullable();
        });
        
        $this->addColumnIfNotExists('transactions', 'attachment', function (Blueprint $table) {
            $table->string('attachment', 500)->nullable();
        });
        
        $this->addColumnIfNotExists('transactions', 'notes', function (Blueprint $table) {
            $table->text('notes')->nullable();
        });
        
        $this->addColumnIfNotExists('transactions', 'recurring_type', function (Blueprint $table) {
            $table->string('recurring_type', 20)->default('none');
        });
        
        $this->addColumnIfNotExists('transactions', 'recurring_until', function (Blueprint $table) {
            $table->date('recurring_until')->nullable();
        });
        
        $this->addColumnIfNotExists('transactions', 'is_transfer', function (Blueprint $table) {
            $table->boolean('is_transfer')->default(false);
        });
        
        $this->addColumnIfNotExists('transactions', 'transfer_transaction_id', function (Blueprint $table) {
            $table->unsignedBigInteger('transfer_transaction_id')->nullable();
        });
        
        $this->addColumnIfNotExists('transactions', 'priority', function (Blueprint $table) {
            $table->string('priority', 20)->default('normal');
        });
        
        $this->addColumnIfNotExists('transactions', 'location', function (Blueprint $table) {
            $table->string('location')->nullable();
        });
        
        // Tentar adicionar índices de forma segura
        $this->addIndexSafely('transactions', ['transaction_date'], 'idx_transactions_date');
        $this->addIndexSafely('transactions', ['bank_account_id'], 'idx_transactions_bank_account');
        $this->addIndexSafely('transactions', ['category_id'], 'idx_transactions_category');
        $this->addIndexSafely('transactions', ['status'], 'idx_transactions_status');
    }

    /**
     * Adiciona uma coluna apenas se ela não existir
     */
    private function addColumnIfNotExists($table, $column, $callback)
    {
        if (!Schema::hasColumn($table, $column)) {
            Schema::table($table, function (Blueprint $table) use ($callback) {
                $callback($table);
            });
        }
    }
    
    /**
     * Adiciona índice de forma segura
     */
    private function addIndexSafely($table, $columns, $indexName)
    {
        try {
            // Verificar se o índice já existe
            $connection = Schema::getConnection();
            $schemaBuilder = $connection->getSchemaBuilder();
            
            // Para SQLite, verificar de forma diferente
            if ($connection->getDriverName() === 'sqlite') {
                $indexes = $connection->select("PRAGMA index_list($table)");
                $indexExists = collect($indexes)->contains('name', $indexName);
            } else {
                // Para MySQL/PostgreSQL
                $indexExists = collect($schemaBuilder->getIndexes($table))->contains('name', $indexName);
            }
            
            if (!$indexExists) {
                Schema::table($table, function (Blueprint $table) use ($columns, $indexName) {
                    $table->index($columns, $indexName);
                });
            }
        } catch (Exception $e) {
            // Se falhar, continuar sem o índice
            \Log::warning("Failed to create index $indexName: " . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover colunas se existirem
        $columns = [
            'category_id', 'tags', 'attachment', 'notes', 
            'recurring_type', 'recurring_until', 'is_transfer', 
            'transfer_transaction_id', 'priority', 'location'
        ];
        
        foreach ($columns as $column) {
            if (Schema::hasColumn('transactions', $column)) {
                Schema::table('transactions', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
        
        // Remover índices se existirem
        $indexes = [
            'idx_transactions_date',
            'idx_transactions_bank_account', 
            'idx_transactions_category',
            'idx_transactions_status'
        ];
        
        foreach ($indexes as $index) {
            try {
                Schema::table('transactions', function (Blueprint $table) use ($index) {
                    $table->dropIndex($index);
                });
            } catch (Exception $e) {
                // Ignorar se o índice não existir
            }
        }
    }
}
