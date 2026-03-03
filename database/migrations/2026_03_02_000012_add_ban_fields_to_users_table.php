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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('banned_comment_id')->nullable()->after('role');
            $table->text('banned_comment_text')->nullable()->after('banned_comment_id');
            $table->text('appeal_message')->nullable()->after('banned_comment_text');
            $table->timestamp('appealed_at')->nullable()->after('appeal_message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['banned_comment_id', 'banned_comment_text', 'appeal_message', 'appealed_at']);
        });
    }
};
