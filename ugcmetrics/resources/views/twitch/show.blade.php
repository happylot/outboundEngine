@php
    // Mapa idioma (ISO 639-1) → país (ISO 3166-1 alpha2)
    $flagMap = [
        'en' => 'gb', // inglés → Reino Unido (union jack)
        'es' => 'es', // español → España
        'pt' => 'pt', // portugués → Portugal
        'fr' => 'fr', // francés → Francia
        'de' => 'de', // alemán → Alemania
        'ja' => 'jp', // japonés → Japón
        'zh' => 'cn', // chino → China
        'ru' => 'ru', // ruso → Rusia
        // añade los que necesites…
    ];
@endphp
<x-layouts.app :title="__('Perfil de Twitch: ' . $profile->username)">
    <div class="container mx-auto px-4 py-8">
        <!-- Alertas -->
        @if (session('info'))
            <div class="bg-blue-100 dark:bg-blue-900 border border-blue-400 dark:border-blue-700 text-blue-700 dark:text-blue-300 px-4 py-3 rounded relative mb-6"
                role="alert">
                <span class="block sm:inline">{{ session('info') }}</span>
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded relative mb-6"
                role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!--
        @if (session('error'))
<div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded relative mb-6"
            role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
@endif
        -->

        <!-- Encabezado con navegación y botón de actualizar -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div class="flex items-center">
                <a href="{{ route('twitch.index') }}"
                    class="mr-4 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-bold py-2 px-4 rounded inline-flex items-center transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver
                </a>
                <h1 class="text-2xl md:text-3xl font-bold text-purple-700 dark:text-purple-400">Perfil de Twitch:
                    {{ $profile->username }}</h1>
            </div>
            <a href="{{ route('twitch.fetch', $profile->username) }}"
                class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded inline-flex items-center transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Actualizar datos
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 mb-8">
            <!-- Sidebar con información del perfil -->
            <div class="lg:col-span-1">
                <div
                    class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700 transition-colors">
                    <div class="flex flex-col items-center text-center mb-4">
                        <div class="relative inline-block mb-4">
                            @if ($profile->profile_picture)
                                <img src="{{ $profile->profile_picture }}"
                                    class="w-32 h-32 rounded-full object-cover border-4 border-purple-500 hover:scale-105 transition-transform"
                                    alt="{{ $profile->username }}">
                            @elseif($profile->influencer && $profile->influencer->profile_picture_url)
                                <img src="{{ asset('storage/' . $profile->influencer->profile_picture_url) }}"
                                    class="w-32 h-32 rounded-full object-cover border-4 border-purple-500 hover:scale-105 transition-transform"
                                    alt="{{ $profile->username }}">
                            @else
                                <div
                                    class="w-32 h-32 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center border-4 border-purple-500">
                                    <span class="text-4xl text-purple-600 dark:text-purple-400">
                                        {{ substr($profile->username, 0, 1) }}
                                    </span>
                                </div>
                            @endif

                            @php
                                $latest = $profile->twitchMetrics->first();
                            @endphp

                            {{-- INDICADOR “EN VIVO” --}}
                            @if (optional($latest)->is_live)
                                <span class="absolute top-0 right-0 block w-10 h-10">
                                    <span
                                        class="absolute inline-flex w-full h-full rounded-full bg-sky-400 opacity-75 animate-ping"></span>
                                    <span class="relative inline-flex w-full h-full rounded-full bg-sky-500"></span>
                                </span>
                            @endif
                        </div>

                        <h2 class="text-2xl font-semibold dark:text-white">
                            {{ $profile->influencer->name ?? ($profile->extra_data['display_name'] ?? $profile->username) }}
                        </h2>
                        <p class="text-purple-600 dark:text-purple-400">{{ '@' . $profile->username }}</p>
                        <div class="mt-2">
                            <a href="{{ $profile->profile_url }}" target="_blank"
                                class="text-blue-600 dark:text-blue-400 hover:underline inline-flex items-center hover:opacity-80 transition-opacity">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                Perfil de Twitch
                            </a>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        @if (isset($profile->extra_data['description']))
                            <p class="text-gray-700 dark:text-gray-300 mb-4">{{ $profile->extra_data['description'] }}
                            </p>
                        @elseif($profile->influencer && $profile->influencer->bio)
                            <p class="text-gray-700 dark:text-gray-300 mb-4">{{ $profile->influencer->bio }}</p>
                        @endif

                        <div class="grid grid-cols-1 gap-4 mb-4">
                            <div class="bg-gray-50 dark:bg-zinc-700 p-4 rounded text-center hover:shadow-md transition">
                                <span class="block text-sm text-gray-500 dark:text-gray-400">Seguidores</span>
                                <p class="mt-2 text-4xl font-bold text-purple-600 dark:text-purple-400">
                                    {{ number_format($latestMetrics->followers ?? 0) }}
                                </p>
                            </div>
                            <div class="bg-gray-50 dark:bg-zinc-700 p-4 rounded text-center hover:shadow-md transition">
                                <span class="block text-sm text-gray-500 dark:text-gray-400">Vistas</span>
                                <p class="mt-2 text-2xl font-bold dark:text-white">
                                    {{ number_format($profile->extra_data['view_count'] ?? 0) }}
                                </p>
                            </div>
                        </div>

                        @if ($profile->influencer && $profile->influencer->location)
                            <div class="mb-4">
                                <p class="flex items-center text-gray-700 dark:text-gray-300">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $profile->influencer->location }}
                                </p>
                            </div>
                        @endif

                        @if (!empty($profile->extra_data) && is_array($profile->extra_data))
                            <div class="flex flex-wrap gap-2">
                                @if (isset($profile->extra_data['verified']) && $profile->extra_data['verified'])
                                    <span
                                        class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300 text-xs px-2 py-1 rounded-full">Verificado</span>
                                @endif
                                @if (isset($profile->extra_data['is_partner']) && $profile->extra_data['is_partner'])
                                    <span
                                        class="bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-300 text-xs px-2 py-1 rounded-full">Partner</span>
                                @endif
                                @if (isset($profile->extra_data['is_affiliate']) && $profile->extra_data['is_affiliate'])
                                    <span
                                        class="bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-300 text-xs px-2 py-1 rounded-full">Affiliate</span>
                                @endif
                                @if (isset($profile->extra_data['language']))
                                    <span
                                        class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-300 text-xs px-2 py-1 rounded-full">{{ strtoupper($profile->extra_data['language']) }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="lg:col-span-3">
                <!-- Tarjetas de métricas -->
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
                    <!-- Tarjeta de seguidores -->
                    <div
                        class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
                        <div class="flex flex-col items-center text-center">
                            <div class="rounded-full bg-purple-100 dark:bg-purple-900 p-3 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-8 w-8 text-purple-600 dark:text-purple-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold dark:text-white mb-1">Seguidores</h3>
                            <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                                {{ number_format($latestMetrics->followers ?? 0) }}</p>
                            @if (isset($latestMetrics) && isset($profile->twitchMetrics) && $profile->twitchMetrics->count() > 1)
                                @php
                                    $previousMetrics = $profile->twitchMetrics->skip(1)->first();
                                    $followerChange = $latestMetrics->followers - $previousMetrics->followers;
                                    $changePercentage =
                                        $previousMetrics->followers > 0
                                            ? ($followerChange / $previousMetrics->followers) * 100
                                            : 0;
                                @endphp
                                <p
                                    class="mt-2 {{ $changePercentage >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $changePercentage >= 0 ? '+' : '' }}{{ number_format($followerChange) }}
                                    ({{ number_format($changePercentage, 2) }}%)
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Tarjeta de visualizaciones medias -->
                    <div
                        class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
                        <div class="flex flex-col items-center text-center">
                            <div class="rounded-full bg-blue-100 dark:bg-blue-900 p-3 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold dark:text-white mb-1">Visualizaciones medias</h3>
                            @php
                                $avgViewers =
                                    isset($recentStreams) && $recentStreams->count() > 0
                                        ? $recentStreams->avg('viewer_count')
                                        : 0;
                            @endphp
                            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                                {{ number_format($avgViewers) }}</p>
                        </div>
                    </div>

                    <!-- Tarjeta de espectadores -->
                    <div
                        class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
                        <div class="flex flex-col items-center text-center">
                            <div class="rounded-full bg-green-100 dark:bg-green-900 p-3 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-8 w-8 text-green-600 dark:text-green-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold dark:text-white mb-1">Espectadores Promedio</h3>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                                {{ number_format($latestMetrics->average_viewers ?? 0) }}</p>
                        </div>
                    </div>

                    <!-- Tarjeta de días de retransmisión -->
                    <div
                        class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
                        <div class="flex flex-col items-center text-center">
                            <div class="rounded-full bg-yellow-100 dark:bg-yellow-900 p-3 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-8 w-8 text-yellow-600 dark:text-yellow-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold dark:text-white mb-1">Días de retransmisión</h3>
                            @php
                                $weekAgo = \Carbon\Carbon::now()->subDays(7);
                                $streamDays = isset($recentStreams)
                                    ? $recentStreams
                                        ->filter(function ($stream) use ($weekAgo) {
                                            return $stream->started_at &&
                                                \Carbon\Carbon::parse($stream->started_at)->isAfter($weekAgo);
                                        })
                                        ->groupBy(function ($stream) {
                                            return \Carbon\Carbon::parse($stream->started_at)->format('Y-m-d');
                                        })
                                        ->count()
                                    : 0;
                            @endphp
                            <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">
                                {{ $streamDays }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Última semana</p>
                        </div>
                    </div>

                    <!-- Tarjeta de horas de transmisión -->
                    <div
                        class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
                        <div class="flex flex-col items-center text-center">
                            <div class="rounded-full bg-indigo-100 dark:bg-indigo-900 p-3 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-8 w-8 text-indigo-600 dark:text-indigo-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold dark:text-white mb-1">Horas de transmisión</h3>
                            @php
                                $weeklyHours = isset($recentStreams)
                                    ? $recentStreams
                                            ->filter(function ($stream) use ($weekAgo) {
                                                return $stream->started_at &&
                                                    \Carbon\Carbon::parse($stream->started_at)->isAfter($weekAgo);
                                            })
                                            ->sum('duration_minutes') / 60
                                    : 0;
                            @endphp
                            <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">
                                {{ number_format($weeklyHours, 1) }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Última semana</p>
                        </div>
                    </div>
                </div>

                <!-- Lista de Streams recientes -->
                <div
                    class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700 mb-8">
                    <h3 class="text-xl font-semibold mb-4 dark:text-white flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Streams recientes
                    </h3>

                    @if (isset($recentStreams) && $recentStreams->count() > 0)
                        <div class="overflow-auto">
                            <table class="min-w-full bg-white dark:bg-zinc-800">
                                <thead class="bg-gray-100 dark:bg-zinc-700">
                                    <tr>
                                        <th class="py-3 px-4 text-left dark:text-gray-300">Stream</th>
                                        <th class="py-3 px-4 text-left dark:text-gray-300">Título</th>
                                        <th class="py-3 px-4 text-left dark:text-gray-300">Juego/Categoría</th>
                                        <th class="py-3 px-4 text-right dark:text-gray-300">Duración</th>
                                        <th class="py-3 px-4 text-right dark:text-gray-300">Views</th>
                                        <th class="py-3 px-4 text-center dark:text-gray-300">Lang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentStreams as $stream)
                                        @php
                                            // Construir miniatura
                                            $thumb = null;
                                            if (!empty($stream->thumbnail_url)) {
                                                $thumb = str_replace(
                                                    ['%{width}', '%{height}'],
                                                    ['320', '180'],
                                                    $stream->thumbnail_url,
                                                );
                                            }
                                            $started = $stream->started_at
                                                ? \Carbon\Carbon::parse($stream->started_at)->format('d/m/Y')
                                                : 'N/A';
                                        @endphp

                                        <tr
                                            class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
                                            {{-- Preview + Fecha debajo --}}
                                            <td class="py-3 px-4 group">
                                                <a href="{{ $stream->stream_url }}" target="_blank"
                                                    class="relative block w-40 h-24 rounded overflow-hidden border-2 border-transparent transition-colors group-hover:border-4 group-hover:border-sky-400">
                                                    @if ($stream->is_live)
                                                        <iframe
                                                            src="https://player.twitch.tv/?channel={{ $profile->username }}&parent={{ request()->getHost() }}"
                                                            frameborder="0" allowfullscreen scrolling="no"
                                                            class="w-full h-full object-cover">
                                                        </iframe>
                                                    @elseif($thumb)
                                                        <img src="{{ $thumb }}"
                                                            alt="Preview {{ $stream->title }}"
                                                            class="w-full h-full object-cover">
                                                    @else
                                                        <div
                                                            class="w-full h-full flex items-center justify-center bg-gray-100 dark:bg-zinc-700">
                                                            <span class="text-xs text-gray-400">No preview</span>
                                                        </div>
                                                    @endif

                                                    <div
                                                        class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-12 w-12 text-white opacity-85 transform transition-transform group-hover:scale-125"
                                                            fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M6.5 5.5v9l7-4.5-7-4.5z" />
                                                        </svg>
                                                    </div>
                                                </a>
                                                <p class="mt-2 text-xs font-mono text-gray-500 dark:text-gray-400">
                                                    {{ $started }}</p>
                                            </td>


                                            {{-- Título --}}
                                            <td class="py-3 px-4">
                                                @if ($stream->stream_url)
                                                    <a href="{{ $stream->stream_url }}" target="_blank"
                                                        class="text-blue-500 hover:underline dark:text-blue-400">
                                                        {{ \Illuminate\Support\Str::limit($stream->title, 40) }}
                                                    </a>
                                                @else
                                                    <span class="dark:text-gray-200">
                                                        {{ \Illuminate\Support\Str::limit($stream->title, 40) }}
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- Juego/Categoría --}}
                                            <td class="py-3 px-4 dark:text-gray-200">
                                                {{ $stream->game_name ?? 'Sin categoría' }}
                                            </td>

                                            {{-- Duración --}}
                                            <td class="py-3 px-4 text-right dark:text-gray-200">
                                                @if ($stream->duration_minutes)
                                                    {{ floor($stream->duration_minutes / 60) }}h
                                                    {{ $stream->duration_minutes % 60 }}m
                                                @else
                                                    –
                                                @endif
                                            </td>

                                            {{-- Views --}}
                                            <td class="py-3 px-4 text-right dark:text-gray-200">
                                                {{ number_format($stream->viewer_count ?? 0) }}
                                            </td>

                                            {{-- Idioma --}}
                                            <td class="py-3 px-4 align-middle text-center">
                                                @php
                                                    $lang = strtolower($stream->language ?? '');
                                                    $cc = $flagMap[$lang] ?? null;
                                                @endphp

                                                @if ($cc)
                                                    <span class="inline-flex items-center space-x-1">
                                                        <img src="https://flagcdn.com/48x36/{{ $cc }}.png"
                                                            alt="{{ strtoupper($lang) }}"
                                                            class="w-8 h-6 rounded-sm align-middle">
                                                        <span
                                                            class="align-middle text-sm font-medium">{{ strtoupper($lang) }}</span>
                                                    </span>
                                                @else
                                                    <span class="text-sm font-medium">-</span>
                                                @endif
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 bg-gray-50 dark:bg-zinc-700 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-12 w-12 mx-auto text-gray-400 dark:text-gray-500 mb-3" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">No hay streams disponibles para este perfil.
                            </p>
                            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Intenta actualizar los datos para
                                obtener la información más reciente.</p>
                        </div>
                    @endif
                </div>


                <!-- Reportes mensuales -->
                @if (isset($monthlyReports) && $monthlyReports->count() > 0)
                    <div
                        class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-xl font-semibold dark:text-white mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-500"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Reportes Mensuales
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach ($monthlyReports as $report)
                                @php
                                    $directesMes = 0;
                                    if (isset($recentStreams) && $recentStreams->count() > 0) {
                                        $directesMes = $recentStreams
                                            ->filter(function ($stream) use ($report) {
                                                $dataStream = \Carbon\Carbon::parse($stream->started_at);
                                                return $dataStream->year == $report->year &&
                                                    $dataStream->month == $report->month;
                                            })
                                            ->count();
                                    }
                                @endphp

                                <div
                                    class="border border-gray-100 dark:border-zinc-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <h4 class="font-medium text-lg dark:text-white">
                                        {{ $report->getMonthNameAttribute() }}
                                        {{ $report->year }}</h4>
                                    <div class="grid grid-cols-2 gap-2 mt-3">
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Seguidores</p>
                                            <p class="font-medium dark:text-white">
                                                {{ number_format($report->followers_end) }}</p>
                                            <p
                                                class="text-xs {{ $report->growth_rate >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                {{ $report->growth_rate >= 0 ? '+' : '' }}{{ number_format($report->growth_rate, 1) }}%
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Streams</p>
                                            <p class="font-medium dark:text-white">{{ $directesMes }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Horas</p>
                                            <p class="font-medium dark:text-white">
                                                {{ number_format($report->hours_streamed, 1) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Espectadores</p>
                                            <p class="font-medium dark:text-white">
                                                {{ number_format($report->average_viewers) }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>