<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_views', function (Blueprint $table) {
            $table->timestamp('viewed_at')->default(\Illuminate\Support\Facades\DB::raw('CURRENT_TIMESTAMP'))->change();
        });
    }

    public function down(): void
    {
        Schema::table('blog_views', function (Blueprint $table) {
            $table->timestamp('viewed_at')->nullable(false)->change();
        });
    }
};
