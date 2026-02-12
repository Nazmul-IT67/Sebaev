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
        Schema::create('post_response_video_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('video_comment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reply_id')->nullable()->constrained('post_response_video_comments')->cascadeOnDelete();
            $table->longText('comment');
            $table->timestamps();

            // Consider adding this composite index if you'll frequently query by video_comment_id
            $table->index(['video_comment_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_response_video_comments');
    }
};
