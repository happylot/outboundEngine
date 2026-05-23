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
        Schema::create('twitch_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_profile_id')->constrained()->onDelete('cascade');
        
            // clave única día‑perfil
            $table->date('date');
            $table->unique(['social_profile_id', 'date']);
        
            // MÉTRICAS
            $table->unsignedBigInteger('followers')->nullable();
            $table->unsignedBigInteger('views')->nullable();
            $table->unsignedInteger('subscribers')->nullable();
        
            $table->float('average_viewers')->nullable();
            $table->unsignedInteger('peak_viewers')->nullable();
        
            $table->float('hours_streamed')->nullable();
            $table->unsignedInteger('stream_count')->nullable();
            $table->unsignedInteger('chat_messages')->nullable();
        
            // flags + json
            $table->boolean('is_live')->default(false);
            $table->json('extra_data')->nullable();
        
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('twitch_metrics');
    }
};