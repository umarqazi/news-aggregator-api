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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->longText('content')->nullable();
            $table->string('author')->nullable();
            $table->string('category')->nullable();
            $table->string('source')->nullable();
            $table->date('published_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->fullText('title', 'articles_title_index');
            $table->index('category');
            $table->index('source');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
