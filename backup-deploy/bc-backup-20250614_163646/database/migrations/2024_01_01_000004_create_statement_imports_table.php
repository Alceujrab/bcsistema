<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('statement_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained()->onDelete('cascade');
            $table->string('filename');
            $table->string('file_type');
            $table->integer('total_transactions');
            $table->integer('imported_transactions');
            $table->integer('duplicate_transactions');
            $table->integer('error_transactions');
            $table->enum('status', ['processing', 'completed', 'failed']);
            $table->json('import_log')->nullable();
            $table->foreignId('imported_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statement_imports');
    }
};
