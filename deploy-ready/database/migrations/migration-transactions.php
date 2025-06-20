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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained()->onDelete('cascade');
            $table->date('transaction_date');
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['credit', 'debit']);
            $table->string('reference_number')->nullable();
            $table->string('category')->nullable();
            $table->enum('status', ['pending', 'reconciled', 'error'])->default('pending');
            $table->foreignId('reconciliation_id')->nullable()->constrained()->nullOnDelete();
            $table->string('import_hash')->nullable()->index();
            $table->timestamps();
            
            $table->index(['bank_account_id', 'transaction_date']);
            $table->unique(['bank_account_id', 'import_hash']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};