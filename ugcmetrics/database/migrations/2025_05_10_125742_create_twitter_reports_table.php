<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('twitter_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_profile_id')->constrained()->onDelete('cascade');
            
            $table->unsignedInteger('year');
            $table->unsignedTinyInteger('month');
            
            // Follower metrics
            $table->integer('followers_start')->nullable();
            $table->integer('followers_end')->nullable();
            $table->float('growth_rate')->nullable();
            
            // Tweet metrics
            $table->integer('tweets_count')->nullable();
            $table->integer('likes_count')->nullable();
            $table->integer('comments_count')->nullable();
            $table->integer('retweets_count')->nullable();
            
            // Engagement
            $table->float('avg_engagement_rate')->nullable();
            $table->integer('impressions')->nullable();
            $table->integer('profile_visits')->nullable();
            $table->integer('mentions_count')->nullable();
            
            // Hashtag performance (stored as JSON)
            $table->json('hashtag_performance')->nullable();
            
            $table->timestamps();
            $table->unique(['social_profile_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('twitter_reports');
    }
};