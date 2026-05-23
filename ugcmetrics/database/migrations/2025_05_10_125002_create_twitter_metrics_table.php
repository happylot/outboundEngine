<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('twitter_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_profile_id')->constrained()->onDelete('cascade');
            $table->date('date');
            
            // Twitter specific metrics
            $table->integer('followers')->nullable();
            $table->integer('following')->nullable();
            $table->integer('tweets')->nullable();
            $table->integer('listed')->nullable();
            
            // Engagement metrics
            $table->integer('likes')->nullable();
            $table->integer('comments')->nullable();
            $table->integer('retweets')->nullable();
            $table->float('engagement_rate')->nullable();
            
            // Additional Twitter metrics
            $table->integer('impressions')->nullable();
            $table->integer('profile_visits')->nullable();
            $table->integer('mentions')->nullable();
            
            $table->timestamps();
            $table->unique(['social_profile_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('twitter_metrics');
    }
};