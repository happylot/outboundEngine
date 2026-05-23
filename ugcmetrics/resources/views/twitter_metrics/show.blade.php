<x-layouts.app :title="__('Twitter Metrics')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="flex flex-col items-center justify-center aspect-video rounded-xl border border-blue-300 bg-white shadow-lg dark:bg-neutral-900 dark:border-neutral-700 hover:shadow-xl transition-shadow duration-300 p-4">
                <div class="w-24 h-24 rounded-full overflow-hidden mb-4">
                    <img src="{{ $twitterProfile->profile_picture ? asset('storage/' . $twitterProfile->profile_picture) : asset('storage/images/placeholder-profile.png') }}"
                         alt="Foto de {{ $twitterProfile->username }}"
                         class="w-full h-full object-cover">
                </div>
                <div class="text-center space-y-1">
                    <p class="text-xl font-bold text-neutral-800 dark:text-white">
                        @{{ $twitterProfile->username }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $twitterProfile->influencer->name }}
                    </p>
                </div>
            </div>
            
            <div class="flex flex-col justify-center aspect-video rounded-xl border border-blue-300 bg-white shadow-lg dark:bg-neutral-900 dark:border-neutral-700 p-4">
                <div class="text-center space-y-2">
                    <p class="text-base font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wide">
                        Seguidores
                    </p>
                    <p class="text-4xl font-extrabold text-blue-600 dark:text-blue-400">
                        {{ number_format($twitterProfile->followers_count) }}
                    </p>
                    <div class="h-1 w-10 mx-auto bg-blue-300 rounded-full"></div>
                </div>
            </div>
            
            <div class="flex flex-col justify-center aspect-video rounded-xl border border-blue-300 bg-white shadow-lg dark:bg-neutral-900 dark:border-neutral-700 p-4">
                <div class="text-center space-y-2">
                    <p class="text-base font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wide">
                        Engagement Rate
                    </p>
                    <p class="text-4xl font-extrabold text-blue-600 dark:text-blue-400">
                        {{ number_format($twitterProfile->engagement_rate, 2) }}%
                    </p>
                    <div class="h-1 w-10 mx-auto bg-blue-300 rounded-full"></div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Últimos Tweets -->
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
                <h3 class="text-lg font-semibold text-neutral-800 dark:text-white mb-4">Últimos Tweets</h3>
                <div class="space-y-4">
                    @foreach($twitterProfile->twitterPosts()->latest()->take(5)->get() as $tweet)
                        <div class="bg-white dark:bg-neutral-800 p-4 rounded-lg shadow">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <img src="{{ $twitterProfile->profile_picture ? asset('storage/' . $twitterProfile->profile_picture) : asset('storage/images/placeholder-profile.png') }}"
                                         class="h-10 w-10 rounded-full">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        @{{ $twitterProfile->username }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $tweet->published_at->diffForHumans() }}
                                    </p>
                                    <p class="mt-1 text-sm text-gray-800 dark:text-gray-200">
                                        {{ $tweet->content }}
                                    </p>
                                    <div class="mt-2 flex space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                        <span class="flex items-center">
                                            <x-heroicon-s-heart class="h-4 w-4 mr-1 text-red-500" />
                                            {{ number_format($tweet->likes) }}
                                        </span>
                                        <span class="flex items-center">
                                            <x-heroicon-s-chat-bubble-left-ellipsis class="h-4 w-4 mr-1 text-blue-500" />
                                            {{ number_format($tweet->comments) }}
                                        </span>
                                        <span class="flex items-center">
                                            <x-heroicon-s-arrow-path class="h-4 w-4 mr-1 text-green-500" />
                                            {{ number_format($tweet->retweets) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Métricas -->
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
                <h3 class="text-lg font-semibold text-neutral-800 dark:text-white mb-4">Métricas Recientes</h3>
                <div class="space-y-4">
                    @foreach($twitterProfile->twitterMetrics()->latest()->take(5)->get() as $metric)
                        <div class="bg-white dark:bg-neutral-800 p-4 rounded-lg shadow">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ $metric->date->format('d M Y') }}
                            </p>
                            <div class="mt-2 grid grid-cols-3 gap-2">
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Seguidores</p>
                                    <p class="font-bold text-blue-600 dark:text-blue-400">{{ number_format($metric->followers) }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Tweets</p>
                                    <p class="font-bold text-blue-600 dark:text-blue-400">{{ number_format($metric->tweets) }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Engagement</p>
                                    <p class="font-bold text-blue-600 dark:text-blue-400">{{ number_format($metric->engagement_rate, 2) }}%</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>