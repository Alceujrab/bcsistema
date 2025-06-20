<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SafeEnhanceCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Adicionar colunas que não existem
            if (!Schema::hasColumn('categories', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->foreign('parent_id')->references('id')->on('categories')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('categories', 'icon')) {
                $table->string('icon')->nullable();
            }
            
            if (!Schema::hasColumn('categories', 'sort_order')) {
                $table->integer('sort_order')->default(0);
            }
            
            if (!Schema::hasColumn('categories', 'budget_limit')) {
                $table->decimal('budget_limit', 15, 2)->nullable();
            }
            
            if (!Schema::hasColumn('categories', 'is_system')) {
                $table->boolean('is_system')->default(false);
            }
            
            if (!Schema::hasColumn('categories', 'rules')) {
                $table->json('rules')->nullable();
            }
        });
        
        // Adicionar índices que não existem
        $existingIndexes = DB::select("SHOW INDEX FROM categories WHERE Key_name = 'categories_parent_id_index'");
        if (empty($existingIndexes)) {
            DB::statement('ALTER TABLE categories ADD INDEX categories_parent_id_index (parent_id)');
        }
        
        $existingIndexes = DB::select("SHOW INDEX FROM categories WHERE Key_name = 'categories_sort_order_index'");
        if (empty($existingIndexes)) {
            DB::statement('ALTER TABLE categories ADD INDEX categories_sort_order_index (sort_order)');
        }
        
        $existingIndexes = DB::select("SHOW INDEX FROM categories WHERE Key_name = 'categories_is_system_index'");
        if (empty($existingIndexes)) {
            DB::statement('ALTER TABLE categories ADD INDEX categories_is_system_index (is_system)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }
            
            if (Schema::hasColumn('categories', 'icon')) {
                $table->dropColumn('icon');
            }
            
            if (Schema::hasColumn('categories', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
            
            if (Schema::hasColumn('categories', 'budget_limit')) {
                $table->dropColumn('budget_limit');
            }
            
            if (Schema::hasColumn('categories', 'is_system')) {
                $table->dropColumn('is_system');
            }
            
            if (Schema::hasColumn('categories', 'rules')) {
                $table->dropColumn('rules');
            }
        });
        
        // Remover índices
        $existingIndexes = DB::select("SHOW INDEX FROM categories WHERE Key_name = 'categories_parent_id_index'");
        if (!empty($existingIndexes)) {
            DB::statement('ALTER TABLE categories DROP INDEX categories_parent_id_index');
        }
        
        $existingIndexes = DB::select("SHOW INDEX FROM categories WHERE Key_name = 'categories_sort_order_index'");
        if (!empty($existingIndexes)) {
            DB::statement('ALTER TABLE categories DROP INDEX categories_sort_order_index');
        }
        
        $existingIndexes = DB::select("SHOW INDEX FROM categories WHERE Key_name = 'categories_is_system_index'");
        if (!empty($existingIndexes)) {
            DB::statement('ALTER TABLE categories DROP INDEX categories_is_system_index');
        }
    }
}
