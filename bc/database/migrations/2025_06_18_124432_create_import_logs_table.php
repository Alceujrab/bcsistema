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
        Schema::create('import_logs', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('path')->nullable();
            $table->foreignId('bank_account_id')->constrained()->onDelete('cascade');
            $table->enum('import_type', ['bank_statement', 'credit_card'])->default('bank_statement');
            $table->integer('total_records')->default(0);
            $table->integer('imported_records')->default(0);
            $table->integer('skipped_records')->default(0);
            $table->string('format')->nullable(); // CSV, OFX, QIF, PDF, Excel
            $table->string('encoding')->nullable();
            $table->string('delimiter')->nullable();
            $table->string('detected_bank')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable(); // Informações adicionais sobre a importação
            $table->timestamps();
            
            $table->index(['bank_account_id', 'status']);
            $table->index(['import_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_logs');
    }
};
