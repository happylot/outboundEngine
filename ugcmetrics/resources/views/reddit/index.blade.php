<x-layouts.app :title="__('Influencers de Reddit')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div
            <!-- This div is for the total influencers card -->
                <class="flex items-center justify-center aspect-video rounded-xl border border-orange-300 bg-white shadow-lg dark:bg-neutral-900 dark:border-neutral-700 hover:shadow-xl transition-shadow duration-300">
                <div class="text-center space-y-2">
                    <p class="text-base font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wide">
                        Total Influencers de Reddit
                    </p>
                    <p class="text-5xl font-extrabold text-orange-500 dark:text-orange-400">
                        {{ count($usernames) }}
                    </p>
                    <div class="h-1 w-10 mx-auto bg-orange-300 rounded-full"></div>
                </div>
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
        </div>

        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
            <h2 class="text-xl font-semibold text-neutral-800 dark:text-white mb-4 text-center">Listado de Influencers de Reddit</h2>

            @if(session('error'))
                <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded relative mb-4 max-w-2xl mx-auto" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if(count($usernames) > 0)
                <ul class="space-y-3 max-w-2xl mx-auto">
                    @foreach($usernames as $username)
                        <li>
                            <a href="{{ route('reddit.show', $username) }}"
                               class="block bg-white dark:bg-neutral-800 rounded-lg shadow-sm hover:bg-orange-50 dark:hover:bg-neutral-700 hover:shadow-md transition-all p-4 text-lg font-medium text-[#0079D3] dark:text-[#60A5FA] hover:text-[#FF4500]">
                                u/{{ $username }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="bg-white dark:bg-neutral-800 shadow-sm rounded-md p-6 text-center">
                    <p class="text-xl text-gray-600 dark:text-gray-300">No se encontraron influencers de Reddit.</p>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">Puedes añadir nuevos influencers desde la sección de gestión de perfiles.</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
