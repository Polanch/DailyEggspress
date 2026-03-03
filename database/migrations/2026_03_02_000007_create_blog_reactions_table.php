<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('reaction_type', 20);
            $table->timestamps();

            $table->unique(['blog_id', 'user_id']);
            $table->index(['blog_id', 'reaction_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_reactions');
    }
};
