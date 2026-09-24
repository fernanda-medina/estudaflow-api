<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mapa_nos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mapa_mental_id')->constrained('mapas_mentais')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable();
            $table->string('label');
            $table->integer('x');
            $table->integer('y');
            $table->string('cor', 20)->nullable();
            $table->timestamps();
        });

        Schema::table('mapa_nos', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('mapa_nos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mapa_nos');
    }
};