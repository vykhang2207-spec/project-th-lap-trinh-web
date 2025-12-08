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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            // Liên kết với Chương học
            $table->foreignId('chapter_id')->constrained('chapters')->onDelete('cascade');
            $table->string('title');
            $table->string('video_url'); // Link video Google Drive embed
            $table->integer('order_index')->default(0); // Thứ tự bài học
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
