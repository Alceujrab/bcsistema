<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class EnhanceCategoriesTableSafe extends Migration
{
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            // Verificar e adicionar colunas que não existem
            if (!Schema::hasColumn('categories', 'color')) {
                $table->string('color', 7)->default('#3B82F6')->after('name');
            }
            
            if (!Schema::hasColumn('categories', 'icon')) {
                $table->string('icon')->nullable()->after('color');
            }
            
            if (!Schema::hasColumn('categories', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description');
            }
            
            if (!Schema::hasColumn('categories', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_active');
            }
            
            if (!Schema::hasColumn('categories', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            }
        });

        // Verificar e criar índices que não existem
        $existingIndexes = $this->getExistingIndexes();
        
        Schema::table('categories', function (Blueprint $table) use ($existingIndexes) {
            // Adicionar foreign key para parent_id se não existir
            if (Schema::hasColumn('categories', 'parent_id') && !in_array('categories_parent_id_foreign', $existingIndexes)) {
                $table->foreign('parent_id')->references('id')->on('categories')->onDelete('set null');
            }
            
            // Só adicionar índices que não existem
            if (!in_array('categories_is_active_sort_order_index', $existingIndexes)) {
                $table->index(['is_active', 'sort_order'], 'categories_is_active_sort_order_index');
            }
            
            if (!in_array('categories_parent_id_index', $existingIndexes)) {
                $table->index('parent_id', 'categories_parent_id_index');
            }
        });

        // Atualizar dados existentes
        DB::table('categories')
            ->whereNull('is_active')
            ->update(['is_active' => true]);
            
        DB::table('categories')
            ->whereNull('sort_order')
            ->update(['sort_order' => 0]);
    }

    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            // Remover foreign keys e índices criados
            $existingIndexes = $this->getExistingIndexes();
            
            if (in_array('categories_parent_id_foreign', $existingIndexes)) {
                $table->dropForeign('categories_parent_id_foreign');
            }
            
            if (in_array('categories_is_active_sort_order_index', $existingIndexes)) {
                $table->dropIndex('categories_is_active_sort_order_index');
            }
            
            if (in_array('categories_parent_id_index', $existingIndexes)) {
                $table->dropIndex('categories_parent_id_index');
            }
            
            // Remover colunas adicionadas
            $columnsToRemove = ['parent_id', 'sort_order', 'is_active', 'icon', 'color'];
            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('categories', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    private function getExistingIndexes()
    {
        $indexes = DB::select("SHOW INDEX FROM categories");
        return collect($indexes)->pluck('Key_name')->toArray();
    }
}
