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
        Schema::create('slides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apresentacao_id')->constrained('apresentacoes')->onDelete('cascade'); 
            $table->integer('ordem'); 
            $table->string('titulo'); 
            $table->text('corpo')->nullable(); 
            $table->unique(['apresentacao_id', 'ordem']); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slides');
    }
};
