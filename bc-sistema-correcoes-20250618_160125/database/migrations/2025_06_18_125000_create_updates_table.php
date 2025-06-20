<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('updates', function (Blueprint $table) {
            $table->id();
            $table->string('version')->unique();
            $table->string('name');
            $table->text('description');
            $table->timestamp('release_date');
            $table->string('download_url')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('file_hash')->nullable();
            $table->json('changes')->nullable(); // Lista de mudanças
            $table->json('requirements')->nullable(); // Requisitos do sistema
            $table->enum('status', [
                'available', 
                'downloading', 
                'ready', 
                'applying', 
                'applied', 
                'failed'
            ])->default('available');
            $table->timestamp('applied_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'release_date']);
            $table->index('version');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('updates');
    }
};
