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
        Schema::create('twitch_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_profile_id')->constrained()->onDelete('cascade');
            $table->integer('year');
            $table->integer('month');
            
            // Datos de métricas básicas
            $table->integer('followers_start')->nullable();
            $table->integer('followers_end')->nullable();
            $table->float('growth_rate')->nullable(); // Porcentaje de crecimiento
            $table->integer('subscribers_average')->nullable();
            $table->float('average_viewers')->nullable();
            $table->integer('peak_viewers')->nullable();
            $table->float('hours_streamed')->nullable();
            $table->float('streams_per_week')->nullable();
            $table->float('chat_engagement')->nullable(); // Mensajes por minuto promedio
            $table->integer('streams_count')->default(0);
            $table->json('game_distribution')->nullable();
            // Datos de estimación económica
            $table->float('estimated_monthly_revenue_min')->nullable();
            $table->float('estimated_monthly_revenue_max')->nullable();
            $table->float('estimated_sponsor_value_min')->nullable();
            $table->float('estimated_sponsor_value_max')->nullable();
            $table->float('estimated_sponsor_value_optimal')->nullable();
            
            // Otros datos de análisis
            $table->json('top_categories')->nullable(); // Top categorías (juegos)
            
            $table->timestamps();
            
            // Clave única para evitar duplicados
            $table->unique(['social_profile_id', 'year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('twitch_reports');
    }
};