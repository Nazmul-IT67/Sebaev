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
        Schema::create('m_r_video_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('movement_id')->constrained('movements')->cascadeOnDelete();
            $table->foreignId('m_r_video_id') // Shorter column name
                ->constrained('movement_response_videos') // Shorter referenced table
                ->cascadeOnDelete();
            $table->foreignId('reply_id')
                ->nullable()
                ->constrained('m_r_video_comments')
                ->cascadeOnDelete();
            $table->longText('comment');
            $table->timestamps();

            // Indexes
            $table->index(['m_r_video_id', 'created_at']);
            $table->index(['user_id', 'created_at']); // Optional but useful
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_r_video_comments');
    }
};
