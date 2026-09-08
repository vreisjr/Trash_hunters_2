<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('categoria_id')->nullable()->after('user_id')
                ->constrained('categorias')->nullOnDelete();

            $table->string('media_path')->nullable()->after('content');
            $table->string('media_type')->nullable()->after('media_path');

            $table->decimal('latitude', 10, 7)->nullable()->after('media_type');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('endereco')->nullable()->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('categoria_id');
            $table->dropColumn(['media_path', 'media_type', 'latitude', 'longitude', 'endereco']);
        });
    }
};
