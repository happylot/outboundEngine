<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Platform;
use App\Models\SocialProfile;
use App\Models\TwitchMetrics;
use App\Models\TwitchStream;
use App\Models\TwitchReports;
use Carbon\Carbon;
use DB;

class GenerateTwitchReports extends Command
{
    protected $signature = 'twitch:generate-reports';
    protected $description = 'Genera reportes mensuales con indicadores clave para perfiles de Twitch';

    public function handle()
    {
        $this->info("Generando reportes mensuales de Twitch...");

        $platform = Platform::where('name', 'Twitch')->first();
        if (!$platform) {
            $this->error('No se encontró la plataforma Twitch.');
            return;
        }

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $profiles = SocialProfile::where('platform_id', $platform->id)->get();

        foreach ($profiles as $profile) {
            $this->info("Procesando perfil: {$profile->username}");
            
            $metrics = TwitchMetrics::where('social_profile_id', $profile->id)
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->get();
            
            if ($metrics->isEmpty()) {
                $this->warn("No hay métricas disponibles para {$profile->username}");
                continue;
            }
            
            // Calcular métricas básicas
            $followersStart = $metrics->sortBy('date')->first()->followers ?? 0;
            $followersEnd = $metrics->sortByDesc('date')->first()->followers ?? 0;
            
            $growthRate = $followersStart > 0 ? 
                round((($followersEnd - $followersStart) / $followersStart) * 100, 2) : 0;
                
            $avgSubscribers = $metrics->avg('subscribers') ?? 0;
            $avgViewers = $metrics->avg('average_viewers') ?? 0;
            $peakViewers = $metrics->max('peak_viewers') ?? 0;
            $hoursStreamed = $metrics->sum('hours_streamed') ?? 0;
            
            // Streams en el período
            $streams = TwitchStream::where('social_profile_id', $profile->id)
                ->whereBetween('started_at', [$startDate, $endDate])
                ->get();
                
            $streamsPerWeek = $startDate->diffInWeeks($endDate) > 0 ?
                round($streams->count() / $startDate->diffInWeeks($endDate), 2) : 0;
                
            // Cálculo de engagement en chat
            $totalMessages = $metrics->sum('chat_messages') ?? 0;
            $totalStreamMinutes = $hoursStreamed * 60;
            $chatEngagement = $totalStreamMinutes > 0 ? 
                round($totalMessages / $totalStreamMinutes, 2) : 0;
                
            // Cálculo de top categorías
            $topCategories = [];
            if ($streams->isNotEmpty()) {
                $gamesCounted = [];
                foreach ($streams as $stream) {
                    if (!empty($stream->game_name)) {
                        $gamesCounted[$stream->game_name] = ($gamesCounted[$stream->game_name] ?? 0) + 1;
                    }
                }
                arsort($gamesCounted);
                $topCategories = array_slice($gamesCounted, 0, 5, true);
            }
            
            // Estimaciones financieras
            $estRevenueMin = ($avgSubscribers * 2.5) + // $2.5 por sub promedio
                             ($avgViewers * $hoursStreamed * 0.005); // $0.005 por hora vista (ads)
                             
            $estRevenueMax = ($avgSubscribers * 3.5) + // $3.5 por sub promedio para partners más grandes
                             ($avgViewers * $hoursStreamed * 0.01); // $0.01 por hora vista (ads)
            
            // Valor de patrocinio basado en viewers y engagement
            $sponsorValueBase = $avgViewers * 0.05; // $0.05 por viewer promedio
            $sponsorValueOptimal = $sponsorValueBase * (1 + ($chatEngagement / 10)); // Ajuste por engagement
            $sponsorValueMin = $sponsorValueOptimal * 0.7;
            $sponsorValueMax = $sponsorValueOptimal * 1.5;

            TwitchReports::updateOrCreate([
                'social_profile_id' => $profile->id,
                'year' => $startDate->year,
                'month' => $startDate->month,
            ], [
                'followers_start' => $followersStart,
                'followers_end' => $followersEnd,
                'growth_rate' => $growthRate,
                'subscribers_average' => round($avgSubscribers),
                'average_viewers' => round($avgViewers, 2),
                'peak_viewers' => $peakViewers,
                'hours_streamed' => round($hoursStreamed, 2),
                'streams_per_week' => $streamsPerWeek,
                'chat_engagement' => $chatEngagement,
                'estimated_monthly_revenue_min' => round($estRevenueMin, 2),
                'estimated_monthly_revenue_max' => round($estRevenueMax, 2),
                'estimated_sponsor_value_min' => round($sponsorValueMin, 2),
                'estimated_sponsor_value_max' => round($sponsorValueMax, 2),
                'estimated_sponsor_value_optimal' => round($sponsorValueOptimal, 2),
                'top_categories' => $topCategories,
            ]);
            
            $this->info("✔ Reporte generado para {$profile->username}");
        }

        $this->info('✅ Reportes de Twitch generados correctamente.');
    }
}