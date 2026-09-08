<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comentario_curtidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comentario_id')->constrained('comentarios')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['comentario_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comentario_curtidas');
    }
};