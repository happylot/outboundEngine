<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use App\Models\SocialProfile;
use App\Models\TwitchMetrics;
use App\Models\TwitchReports;
use App\Models\TwitchStream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TwitchController extends Controller
{
    /**
     * Display a listing of Twitch profiles.
     */
    public function index()
    {
        // Obtener la plataforma de Twitch
        $platform = Platform::where('name', 'Twitch')->first();

        if (!$platform) {
            return view('twitch.index', ['error' => 'Plataforma Twitch no encontrada']);
        }

        // Obtener perfiles de Twitch con sus métricas y streams
        $profiles = SocialProfile::where('platform_id', $platform->id)
            ->with(['influencer', 'twitchMetrics' => function($query) {
                $query->orderBy('date', 'desc')->take(30);
            }, 'twitchStreams' => function($query) {
                $query->orderBy('started_at', 'desc')->take(10);
            }])
            ->get();

        // Obtener reportes mensuales si existen
        $reports = TwitchReports::whereIn('social_profile_id', $profiles->pluck('id'))
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->groupBy('social_profile_id');

        // Obtener influencers únicos asociados a los perfiles
        $influencers = $profiles->pluck('influencer')->unique('id');

        return view('twitch.index', compact('profiles', 'reports', 'influencers'));
    }

    /**
     * Display detailed information for a specific Twitch profile.
     */
    public function show($username)
    {
        // Obtener la plataforma de Twitch
        $platform = Platform::where('name', 'Twitch')->first();
        
        if (!$platform) {
            return redirect()->route('twitch.index')
                ->with('error', 'Plataforma Twitch no encontrada');
        }

        // Obtener el perfil con sus relaciones
        $profile = SocialProfile::where('platform_id', $platform->id)
            ->where('username', $username)
            ->with(['influencer', 
                    'twitchMetrics' => function($query) {
                        $query->orderBy('date', 'desc');
                    },
                    'twitchStreams' => function($query) {
                        $query->orderBy('started_at', 'desc');
                    },
                    'twitchReports' => function($query) {
                        $query->orderBy('year', 'desc')->orderBy('month', 'desc');
                    }
            ])
            ->first();
        
        if (!$profile) {
            return redirect()->route('twitch.index')
                ->with('error', 'Perfil de Twitch no encontrado: ' . $username);
        }
        
        // Obtener las métricas más recientes
        $latestMetrics = $profile->twitchMetrics->first();
        
        // Obtener métricas para gráfico (últimos 30 días)
        $metricsForChart = $profile->twitchMetrics
            ->sortBy('date')
            ->take(30)
            ->map(function($metric) {
                return [
                    'date' => $metric->date->format('Y-m-d'),
                    'followers' => $metric->followers,
                    'views' => $metric->views,
                    'average_viewers' => $metric->average_viewers
                ];
            })
            ->values();
        
        // Obtener streams recientes (últimos 10)
        $recentStreams = $profile->twitchStreams
            ->take(10);
        
        // Obtener reportes mensuales (últimos 6 meses)
        $monthlyReports = $profile->twitchReports
            ->take(6);
        
        return view('twitch.show', compact(
            'profile', 
            'latestMetrics', 
            'metricsForChart', 
            'recentStreams', 
            'monthlyReports'
        ));
    }

    /**
     * Get access token for Twitch API.
     */
    public function getAccessToken()
    {
        // Si ya tenemos un token válido en caché, lo devolvemos
        if (Cache::has('twitch_access_token')) {
            return Cache::get('twitch_access_token');
        }

        $clientId = env('TWITCH_CLIENT_ID');
        $clientSecret = env('TWITCH_CLIENT_SECRET');
        
        if (!$clientId || !$clientSecret) {
            throw new \Exception('Faltan las credenciales de Twitch en el archivo .env');
        }

        try {
            $ch = curl_init('https://id.twitch.tv/oauth2/token');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'grant_type' => 'client_credentials'
            ]));
            
            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                throw new \Exception('Error cURL: ' . curl_error($ch));
            }
            
            curl_close($ch);
            $data = json_decode($response, true);
            
            if (!isset($data['access_token'])) {
                throw new \Exception('Respuesta inválida al solicitar token: ' . $response);
            }
            
            // Guardamos el token en caché con una duración menor a la expiración
            $expiresIn = $data['expires_in'] ?? 14400; // Por defecto 4 horas
            Cache::put('twitch_access_token', $data['access_token'], now()->addSeconds($expiresIn - 300));
            
            return $data['access_token'];
        } catch (\Exception $e) {
            \Log::error('Error al generar token de Twitch: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Fetch data from Twitch API for the specified profile.
     */
    public function fetch($username)
    {
        try {
            $accessToken = $this->getAccessToken();
            $clientId = env('TWITCH_CLIENT_ID');
            
            if (!$accessToken || !$clientId) {
                throw new \Exception('Faltan configuraciones de API de Twitch');
            }
            
            // 1. Obtener datos del perfil
            $userData = $this->getUserData($username, $accessToken, $clientId);
            
            // 2. Verificar si está en streaming ahora
            $liveStreamData = $this->getStreamData($username, $accessToken, $clientId);
            
            // 3. Obtener datos de streams pasados
            $streamsData = $this->getStreamsData($username, $accessToken, $clientId);
            
            // 4. Obtener datos de seguidores
            $followersData = $this->getFollowersData($userData['id'], $accessToken, $clientId);
            
            // 5. Guardar en la base de datos
            $profile = $this->saveUserData($userData, $username, $followersData);
            $this->saveStreamsData($streamsData, $profile->id);
            
            // 6. Si está en vivo, actualizar métricas con datos en tiempo real
            if ($liveStreamData) {
                $this->updateLiveStreamMetrics($liveStreamData, $profile->id);
            }
            
            // 7. Generar informes mensuales
            $this->generateReports($username);
            
            return redirect()->route('twitch.show', $username)
                ->with('success', 'Datos de Twitch actualizados correctamente');
                
        } catch (\Exception $e) {
            \Log::error('Error al obtener datos de Twitch: ' . $e->getMessage());
            return redirect()->route('twitch.show', $username)
                ->with('error', 'Error al obtener datos: ' . $e->getMessage());
        }
    }

    /**
     * Alias para el método fetch() para mantener compatibilidad con las rutas existentes.
     */
    public function fetchData($username)
    {
        return $this->fetch($username);
    }

    /**
     * Get user data from Twitch API.
     */
    private function getUserData($username, $accessToken, $clientId)
    {
        $url = "https://api.twitch.tv/helix/users?login={$username}";
        
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Client-Id: ' . $clientId
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            throw new \Exception('Error cURL al obtener datos del usuario: ' . curl_error($ch));
        }
        
        curl_close($ch);
        $data = json_decode($response, true);
        
        
        // Añadir esta línea para registrar la respuesta completa durante la depuración
        \Log::debug('Respuesta de API Twitch getUserData:', ['username' => $username, 'data' => $data]);
        
        if (empty($data['data'])) {
            throw new \Exception('Usuario no encontrado en Twitch: ' . $username);
        }
        
        return $data['data'][0];
    }

    /**
     * Get streams data from Twitch API.
     */
    private function getStreamsData($username, $accessToken, $clientId)
    {
        try {
            // Primero obtenemos el ID del usuario
            $userData = $this->getUserData($username, $accessToken, $clientId);
            $userId = $userData['id'];
            
            // Ahora consultamos los videos recientes
            $url = "https://api.twitch.tv/helix/videos?user_id={$userId}&first=10&type=archive";
            
            $headers = [
                'Authorization: Bearer ' . $accessToken,
                'Client-Id: ' . $clientId
            ];
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            
            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                \Log::warning('Error al obtener datos de streams: ' . curl_error($ch));
                return [];
            }
            
            curl_close($ch);
            $data = json_decode($response, true);
            
            // Transformar los datos para que coincidan con nuestro modelo
            $streams = [];
            foreach ($data['data'] ?? [] as $video) {
                // Agregar valores por defecto para evitar errores
                $streams[] = [
                    'id' => $video['id'] ?? uniqid(),
                    'title' => $video['title'] ?? 'Sin título',
                    'game_id' => $video['game_id'] ?? null,
                    'game_name' => $video['game_name'] ?? 'Sin categoría', 
                    'viewer_count' => $video['view_count'] ?? 0,
                    'language' => $video['language'] ?? 'es',
                    'thumbnail_url' => $video['thumbnail_url'] ?? '',
                    'url' => $video['url'] ?? "https://twitch.tv/{$username}/videos",
                    'started_at' => $video['created_at'] ?? now()->toIso8601String(),
                    'duration' => $this->convertDurationToSeconds($video['duration'] ?? '0h0m0s')
                ];
            }
            
            return $streams;
        } catch (\Exception $e) {
            \Log::error('Error al obtener datos de streams: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Helper para convertir duración en formato "1h2m3s" a segundos
     */
    private function convertDurationToSeconds($duration)
    {
        $seconds = 0;
        
        // Extraer horas
        if (preg_match('/(\d+)h/', $duration, $matches)) {
            $seconds += $matches[1] * 3600;
        }
        
        // Extraer minutos
        if (preg_match('/(\d+)m/', $duration, $matches)) {
            $seconds += $matches[1] * 60;
        }
        
        // Extraer segundos
        if (preg_match('/(\d+)s/', $duration, $matches)) {
            $seconds += $matches[1];
        }
        
        return $seconds;
    }

    /**
     * Check if user is currently streaming and get stream data.
     */
    private function getStreamData($username, $accessToken, $clientId)
    {
        $url = "https://api.twitch.tv/helix/streams?user_login={$username}";
        
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Client-Id: ' . $clientId
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            \Log::warning('Error al obtener datos de streams en vivo: ' . curl_error($ch));
            return null;
        }
        
        curl_close($ch);
        $data = json_decode($response, true);
        
        return $data['data'][0] ?? null;  // Devuelve el stream actual o null si no está en vivo
    }

    /**
     * Get followers data from Twitch API.
     */
    private function getFollowersData($userId, $accessToken, $clientId)
    {
        $url = "https://api.twitch.tv/helix/channels/followers?broadcaster_id={$userId}";
        
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Client-Id: ' . $clientId
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            \Log::warning('Error al obtener datos de seguidores: ' . curl_error($ch));
            return ['total' => 0];
        }
        
        curl_close($ch);
        $data = json_decode($response, true);
        
        return [
            'total' => $data['total'] ?? 0,
            'recent' => $data['data'] ?? []
        ];
    }

    /**
     * Save user data to database.
     */

private function saveUserData(array $userData, string $username, ?array $followersData = null): SocialProfile
{
    $platform = Platform::firstOrCreate(['name' => 'Twitch'], [
        'icon'  => 'fab fa-twitch',
        'color' => '#6441a5',
    ]);

    /** @var SocialProfile $profile */
    $profile = SocialProfile::firstOrCreate(
        ['platform_id' => $platform->id, 'username' => $username],
        ['profile_url' => "https://twitch.tv/{$username}"]
    );

    // contador total de seguidores (viene en users/follows?to_id)
    $totalFollowers = $followersData['total'] ?? 0;

    $profile->update([
        'followers_count' => $totalFollowers,
        'profile_picture' => $userData['profile_image_url'] ?? null,
        'extra_data'      => [
            'display_name'      => $userData['display_name']      ?? $username,
            'description'       => $userData['description']       ?? '',
            'view_count'        => $userData['view_count']        ?? 0,
            'broadcaster_type'  => $userData['broadcaster_type']  ?? '',
            'profile_image_url' => $userData['profile_image_url'] ?? '',
            'offline_image_url' => $userData['offline_image_url'] ?? '',
            'created_at'        => $userData['created_at']        ?? '',
            'followers_total'   => $totalFollowers,
        ],
    ]);

    // métrica diaria (1 fila / día / perfil)
    TwitchMetrics::updateOrCreate(
        [
            'social_profile_id' => $profile->id,
            'date'              => now()->toDateString(),
        ],
        [
            'followers'       => $totalFollowers,
            'views'           => $userData['view_count'] ?? 0,
            'average_viewers' => $userData['average_viewers'] ?? null,
            'peak_viewers'    => $userData['peak_viewers']    ?? null,
            'is_live'         => false,
            'extra_data'      => [
                'description'      => $userData['description']      ?? '',
                'broadcaster_type' => $userData['broadcaster_type'] ?? '',
            ],
        ]
    );

    return $profile;
}


    /**
     * Save streams data to database.
     */
    private function saveStreamsData($streamsData, $profileId)
    {
        // Obtener el perfil
        $profile = SocialProfile::where('id', $profileId)->first();
        if (!$profile) {
            \Log::warning("No se encontró el perfil con ID: {$profileId}");
            return;
        }

        foreach ($streamsData as $streamData) {
            // Verificar si existen todas las claves necesarias y proporcionar valores predeterminados
            $gameId = $streamData['game_id'] ?? null;
            $gameName = $streamData['game_name'] ?? 'Sin categoría';
            $title = $streamData['title'] ?? 'Sin título';
            $viewerCount = $streamData['viewer_count'] ?? 0;
            $startedAt = isset($streamData['started_at']) ? \Carbon\Carbon::parse($streamData['started_at']) : now();
            
            // Crear o actualizar el registro de stream
            TwitchStream::updateOrCreate(
                [
                    'social_profile_id' => $profile->id,
                    'stream_id' => $streamData['id'] ?? uniqid(),
                    'started_at' => $startedAt,
                ],
                [
                    'title' => $title,
                    'game_id' => $gameId,
                    'game_name' => $gameName,
                    'viewer_count' => $viewerCount,
                    'thumbnail_url' => $streamData['thumbnail_url'] ?? null,
                    'language' => $streamData['language'] ?? null,
                    'stream_url' => $streamData['url'] ?? "https://twitch.tv/{$profile->username}",
                    'duration_minutes' => isset($streamData['duration']) ? intval($streamData['duration'] / 60) : null,
                    'peak_viewers' => $streamData['peak_viewers'] ?? $viewerCount,
                    'average_viewers' => $streamData['average_viewers'] ?? $viewerCount,
                    'followers_gained' => $streamData['followers_gained'] ?? null,
                ]
            );
        }
    }

    /**
     * Get game name from game ID.
     */
    private function getGameName($gameId, $accessToken, $clientId)
    {
        if (empty($gameId)) {
            return 'Sin categoría';
        }
        
        $cacheKey = "twitch_game_{$gameId}";
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        $url = "https://api.twitch.tv/helix/games?id={$gameId}";
        
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Client-Id: ' . $clientId
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            return 'Desconocido';
        }
        
        curl_close($ch);
        $data = json_decode($response, true);
        
        $gameName = empty($data['data']) ? 'Desconocido' : $data['data'][0]['name'];
        
        Cache::put($cacheKey, $gameName, now()->addDays(7));
        
        return $gameName;
    }

    /**
     * Generate monthly reports for a Twitch profile.
     */
    private function generateReports($username)
    {
        // Obtener el perfil
        $platform = Platform::where('name', 'Twitch')->first();
        $profile = SocialProfile::where('platform_id', $platform->id)
            ->where('username', $username)
            ->first();
        
        if (!$profile) {
            return;
        }
        
        // Obtener año y mes actual
        $year = now()->year;
        $month = now()->month;
        
        // Obtener métricas del mes
        $metrics = TwitchMetrics::where('social_profile_id', $profile->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date')
            ->get();
        
        if ($metrics->isEmpty()) {
            return;
        }
        
        // Obtener streams del mes
        $streams = TwitchStream::where('social_profile_id', $profile->id)
            ->whereYear('started_at', $year)
            ->whereMonth('started_at', $month)
            ->get();
        
        // Calcular estadísticas
        $followersStart = $metrics->first()->followers;
        $followersEnd = $metrics->last()->followers;
        $growthRate = $followersStart > 0 ? (($followersEnd - $followersStart) / $followersStart) * 100 : 0;
        
        $averageViewers = $metrics->avg('average_viewers');
        $peakViewers = $metrics->max('peak_viewers');
        $hoursStreamed = $streams->sum('duration_minutes') / 60;
        
        // Calcular distribución de juegos
        $gameDistribution = [];
        foreach ($streams as $stream) {
            $gameName = $stream->game_name ?? 'Sin categoría';
            if (!isset($gameDistribution[$gameName])) {
                $gameDistribution[$gameName] = 0;
            }
            $gameDistribution[$gameName] += $stream->duration_minutes;
        }
        
        // Ordenar distribución de juegos
        arsort($gameDistribution);
        
        // Guardar o actualizar reporte
        TwitchReports::updateOrCreate(
            [
                'social_profile_id' => $profile->id,
                'year' => $year,
                'month' => $month
            ],
            [
                'followers_start' => $followersStart,
                'followers_end' => $followersEnd,
                'growth_rate' => $growthRate,
                'average_viewers' => $averageViewers,
                'peak_viewers' => $peakViewers,
                'hours_streamed' => $hoursStreamed,
                'streams_count' => $streams->count(),
                'game_distribution' => $gameDistribution,
            ]
        );
    }

    /**
     * Update metrics with live stream data.
     */
    private function updateLiveStreamMetrics($streamData, $profileId)
    {
        $metrics = TwitchMetrics::where('social_profile_id', $profileId)
            ->whereDate('date', now())
            ->first();
        
        if ($metrics) {
            // Asegurarnos de tener valores predeterminados para evitar errores
            $viewerCount = $streamData['viewer_count'] ?? 0;
            $gameName = $streamData['game_name'] ?? 'Sin categoría';
            $title = $streamData['title'] ?? '';
            $thumbnailUrl = $streamData['thumbnail_url'] ?? '';
            $startedAt = $streamData['started_at'] ?? now()->toIso8601String();
            
            // Actualizar con datos en vivo
            $metrics->update([
                'is_live' => true,
                'average_viewers' => $viewerCount,
                'peak_viewers' => max($metrics->peak_viewers ?? 0, $viewerCount),
                'extra_data' => array_merge($metrics->extra_data ?? [], [
                    'game_name' => $gameName,
                    'title' => $title,
                    'thumbnail_url' => $thumbnailUrl,
                    'started_at' => $startedAt,
                ]),
            ]);
        }

    }

     /**
     * Sincroniza datos de Twitch (perfil, streams, métricas y reportes)
     */
     public function syncProfile(string $username): void
    {
        $accessToken = $this->getAccessToken();
        $clientId    = env('TWITCH_CLIENT_ID');

        if (! $accessToken || ! $clientId) {
            throw new \Exception('Faltan configuraciones de API de Twitch');
        }

        // 1. Datos de usuario
        $userData       = $this->getUserData($username, $accessToken, $clientId);
        // 2. Stream en vivo
        $liveData       = $this->getStreamData($username,   $accessToken, $clientId);
        // 3. Últimos vídeos
        $streamsData    = $this->getStreamsData($username,  $accessToken, $clientId);
        // 4. Seguidores
        $followersData  = $this->getFollowersData($userData['id'], $accessToken, $clientId);

        // 5. Guardar todo en BD
        $profile = $this->saveUserData($userData, $username, $followersData);
        $this->saveStreamsData($streamsData, $profile->id);

        // 6. Si está en vivo, actualiza métricas en tiempo real
        if ($liveData) {
            $this->updateLiveStreamMetrics($liveData, $profile->id);
        }

        // 7. Genera informes mensuales
        $this->generateReports($username);
    }

}