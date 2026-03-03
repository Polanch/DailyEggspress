<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guest_token')->nullable();
            $table->timestamp('viewed_at')->index();
            $table->timestamps();

            $table->index(['blog_id', 'user_id', 'viewed_at']);
            $table->index(['blog_id', 'guest_token', 'viewed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_views');
    }
};
