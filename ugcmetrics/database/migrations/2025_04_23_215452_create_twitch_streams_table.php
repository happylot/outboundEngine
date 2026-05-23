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
        Schema::create('twitch_streams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_profile_id')->constrained()->onDelete('cascade');
            $table->string('stream_id')->unique(); // ID externo del stream (API de Twitch)
            $table->string('title')->nullable();
            $table->string('game_id')->nullable();
            $table->string('game_name')->nullable();
            $table->string('stream_url')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->integer('viewer_count')->nullable(); // Contador total de viewers
            $table->integer('peak_viewers')->nullable(); // Pico de viewers
            $table->float('average_viewers')->nullable(); // Promedio de viewers
            $table->integer('followers_gained')->nullable(); // Followers ganados durante el stream
            $table->integer('chat_messages')->nullable(); // Número de mensajes en el chat
            $table->boolean('is_mature')->default(false); // Contenido para adultos
            $table->string('language')->nullable(); // Idioma del stream
            $table->json('tags')->nullable(); // Tags del stream
            $table->boolean('is_sponsored')->default(false); // Si es contenido patrocinado
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('twitch_streams');
    }
};