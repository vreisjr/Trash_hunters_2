<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('posts', 'edited_at')) {
            return;
        }

        Schema::table('posts', function (Blueprint $table) {
            $table->dateTime('edited_at')->nullable();
        });
    }

    public function down(): void
    {
        //
    }
};