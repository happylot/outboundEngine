<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas de {{ $user['username'] }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen">
    <div class="container mx-auto p-4 sm:p-6 max-w-3xl pt-4">
        <a href="{{ route('reddit.index') }}" 
           class="inline-flex items-center px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors duration-200 mb-4 font-medium text-lg">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Volver al listado
        </a>

        <div class="bg-white dark:bg-gray-800 p-8 sm:p-10 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300"> 
            <div class="flex items-center space-x-6 mb-4">
                @php
                    // Default avatar URL if none provided
                    $avatarUrl = $user['avatar'] ?? 'https://via.placeholder.com/128'; // Placeholder image
                @endphp
                <img src="{{ $avatarUrl }}" alt="Avatar" 
                     class="w-32 h-32 rounded-full border-4 border-orange-500 dark:border-orange-700 object-cover hover:scale-105 transition-transform duration-200">
                <h1 class="text-4xl sm:text-5xl font-bold text-orange-600 dark:text-orange-400">{{ $user['username'] }}</h1>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow flex flex-col items-center text-center">
                    <div class="rounded-full bg-orange-100 dark:bg-orange-900 p-3 mb-3">
                        <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold dark:text-white mb-2">Links</h3>
                    <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $user['link_karma'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow flex flex-col items-center text-center">
                    <div class="rounded-full bg-orange-100 dark:bg-orange-900 p-3 mb-3">
                        <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5v-4h4v4zM3 12h18"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold dark:text-white mb-2">Comentarios</h3>
                    <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $user['comment_karma'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow flex flex-col items-center text-center">
                    <div class="rounded-full bg-orange-100 dark:bg-orange-900 p-3 mb-3">
                        <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold dark:text-white mb-2">Total</h3>
                    <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $user['total_karma'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow flex flex-col items-center text-center">
                    <div class="rounded-full bg-orange-100 dark:bg-orange-900 p-3 mb-3">
                        <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold dark:text-white mb-2">Creado el</h3>
                    <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $user['created_utc'] }}</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>