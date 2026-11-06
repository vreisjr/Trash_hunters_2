<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('posts')
            ->where('price_type', 'paid')
            ->update(['post_type' => 'marketplace']);
    }

    public function down(): void
    {
        DB::table('posts')
            ->where('post_type', 'marketplace')
            ->where('price_type', 'paid')
            ->update(['post_type' => 'feed']);
    }
};
