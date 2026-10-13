<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coletas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doador_id')->constrained('users')->cascadeOnDelete(); // quem postou/doou
            $table->foreignId('coletor_id')->constrained('users')->cascadeOnDelete(); // quem coletou
            $table->unsignedBigInteger('post_id')->nullable(); // post de origem (opcional)
            $table->string('item');                // nome/descrição do item doado
            $table->dateTime('data_hora');         // quando foi doado/coletado
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coletas');
    }
};
