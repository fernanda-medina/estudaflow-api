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
        Schema::create('planilha_celulas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planilha_id')->constrained()->onDelete('cascade');
            $table->integer('linha'); 
            $table->integer('coluna'); 
            $table->string('valor')->nullable(); 
            $table->unique(['planilha_id', 'linha', 'coluna']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planilha_celulas');
    }
};
