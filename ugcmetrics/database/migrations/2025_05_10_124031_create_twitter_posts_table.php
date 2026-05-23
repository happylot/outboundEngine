<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('twitter_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_profile_id')->constrained()->onDelete('cascade');
            $table->string('post_id')->unique();
            $table->text('content');
            $table->timestamp('published_at');
            
            // Engagement metrics
            $table->integer('likes')->nullable();
            $table->integer('comments')->nullable();
            $table->integer('retweets')->nullable();
            $table->integer('views')->nullable();
            $table->float('engagement_rate')->nullable();
            
            // Twitter specific fields
            $table->json('media_urls')->nullable();
            $table->json('hashtags')->nullable();
            $table->json('mentions')->nullable();
            $table->boolean('is_retweet')->default(false);
            $table->boolean('is_reply')->default(false);
            $table->string('language')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('twitter_posts');
    }
};