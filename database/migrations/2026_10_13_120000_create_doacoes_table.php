<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('coletor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nome', 150);
            $table->text('descricao')->nullable();
            $table->string('status', 20)->default('disponivel');
            $table->timestamp('reservado_em')->nullable();
            $table->timestamp('coletado_em')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['coletor_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doacoes');
    }
};
