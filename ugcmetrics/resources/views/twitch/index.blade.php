<x-layouts.app :title="__('Perfiles de Twitch')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div
                class="flex items-center justify-center aspect-video rounded-xl border border-purple-300 bg-white shadow-lg dark:bg-neutral-900 dark:border-neutral-700 hover:shadow-xl transition-shadow duration-300">
                <div class="text-center space-y-2">
                    <p class="text-base font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wide">
                        Total Perfiles de Twitch
                    </p>
                    <p class="text-5xl font-extrabold text-purple-600 dark:text-purple-400">
                        {{ count($profiles) }}
                    </p>
                    <div class="h-1 w-10 mx-auto bg-purple-300 rounded-full"></div>
                </div>
            </div>
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern
                    class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern
                    class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
        </div>

        <div
            class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
            <h2 class="text-xl font-semibold text-neutral-800 dark:text-white mb-4">Listado de Perfiles de Twitch</h2>

            @if(isset($error))
                <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error:</strong>
                    <span class="block sm:inline">{{ $error }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="bg-blue-100 dark:bg-blue-900 border border-blue-400 dark:border-blue-700 text-blue-700 dark:text-blue-300 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('info') }}</span>
                </div>
            @endif

            @if(count($profiles) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left border-separate border-spacing-y-2">
                        <thead>
                            <tr class="text-neutral-600 dark:text-neutral-300">
                                <th class="py-3 px-4">Foto</th>
                                <th class="py-3 px-4">Nombre</th>
                                <th class="py-3 px-4">Plataformas</th>
                                <th class="py-3 px-4">Seguidores Totales</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($profiles as $profile)
                                <tr class="bg-white dark:bg-neutral-800 shadow-sm rounded-md hover:bg-gray-100 dark:hover:bg-neutral-700 transition-colors duration-200 cursor-pointer"
                                    onclick="window.location.href='{{ route('twitch.show', $profile->username) }}'">
                                    <td class="py-2 px-4">
                                        <div class="w-[50px] h-[50px]">
                                            <a href="{{ route('twitch.show', $profile->username) }}"
                                               class="w-full h-full rounded-full overflow-hidden block">
                                                @php
                                                    $external = $profile->profile_picture;
                                                    $stored = $profile->influencer->profile_picture_url ?? null;
                                                @endphp

                                                @if($external)
                                                    <img src="{{ $external }}" alt="{{ $profile->username }}"
                                                         class="w-full h-full rounded-full object-cover">
                                                @elseif($stored)
                                                    @if(Str::startsWith($stored, ['http://', 'https://']))
                                                        <img src="{{ $stored }}" alt="{{ $profile->username }}"
                                                             class="w-full h-full rounded-full object-cover">
                                                    @else
                                                        <img src="{{ asset('storage/'.$stored) }}" alt="{{ $profile->username }}"
                                                             class="w-full h-full rounded-full object-cover">
                                                    @endif
                                                @else
                                                    <div class="w-full h-full rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                                                        <span class="text-lg text-purple-600 dark:text-purple-400">
                                                            {{ substr($profile->username, 0, 1) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </a>
                                        </div>
                                    </td>
                                    <td class="py-2 px-4 font-medium text-neutral-800 dark:text-white">
                                        {{ $profile->influencer->name }}
                                    </td>
                                    <td class="py-2 px-4 space-x-1">
                                        <flux:avatar.group class="**:ring-zinc-100 dark:**:ring-zinc-800">
                                            @foreach ($profile->influencer->socialProfiles as $socialProfile)
                                                <a href="{{ $socialProfile->profile_url ?? '#' }}" target="_blank"
                                                   class="text-indigo-600 hover:underline">
                                                    <flux:avatar circle src="{{ asset('storage/img/platform/'. Str::lower($socialProfile->platform->name).'.png') }}" />
                                                </a>
                                            @endforeach
                                        </flux:avatar.group>
                                    </td>
                                    <td class="py-2 px-4 text-neutral-700 dark:text-neutral-300">
                                        {{ number_format($profile->influencer->socialProfiles->sum('followers_count')) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="bg-white dark:bg-neutral-800 shadow-sm rounded-md p-6 text-center">
                    <p class="text-xl text-gray-600 dark:text-gray-300">No se encontraron perfiles con cuentas de Twitch.</p>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">Puede añadir cuentas de Twitch a los influencers desde la sección de gestión de perfiles.</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
