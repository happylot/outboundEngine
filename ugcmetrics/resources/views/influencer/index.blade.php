<x-layouts.app :title="__('Influencer')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-4">
        <!-- Top Stats / Actions Row -->
        <div class="grid gap-6 md:grid-cols-3">
            <x-ui::stat-card
                :value="$influencers->count()"
                :label="__('Total Influencers')"
                variant="primary"
                icon="users"
            />
            
            <x-ui::stat-card
                :value="$influencers->flatMap->socialProfiles->sum('followers_count')"
                :label="__('Aggregate Followers')"
                variant="success"
                icon="globe"
            />

            <!-- Create Action Card -->
            <x-ui::card :bordered="true" padding="sm" class="flex items-center justify-center min-h-[110px]">
                <div class="text-center space-y-2 py-2">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('Manage Creators') }}
                    </p>
                    <x-ui::button 
                        variant="primary" 
                        size="sm"
                        :href="route('influencer.create')"
                        icon="user-plus"
                    >
                        {{ __('Create Influencer') }}
                    </x-ui::button>
                </div>
            </x-ui::card>
        </div>

        <!-- Influencers List Table -->
        <x-ui::card
            :title="__('List of Influencers')"
            :subtitle="__('Overview of registered content creators and their connected social channels.')"
            :bordered="true"
            padding="lg"
        >
            <x-ui::data-table
                :headers="[
                    'photo' => __('Photo'),
                    'name' => __('Name'),
                    'platforms' => __('Platforms'),
                    'followers' => __('Total Followers'),
                    'reddit_karma' => __('Total Karma (Reddit)')
                ]"
                :searchable="true"
                :sortable="false"
                :striped="true"
                :hoverable="true"
                emptyMessage="No influencers found."
            >
                @foreach ($influencers as $influencer)
                    <tr data-searchable>
                        <td class="px-4 py-3">
                            <div class="h-12 w-12 overflow-hidden rounded-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                                <img 
                                    src="{{ $influencer->profile_picture_url ? asset('storage/' . $influencer->profile_picture_url) : asset('storage/img/influencer/placeholder-profile.png') }}" 
                                    alt="Foto de {{ $influencer->name }}"
                                    class="h-full w-full object-cover"
                                />
                            </div>
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">
                            {{ $influencer->name }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1.5">
                                @forelse ($influencer->socialProfiles as $profile)
                                    <a 
                                        href="{{ $profile->profile_url ?? '#' }}" 
                                        target="_blank"
                                        class="inline-flex items-center gap-1 rounded bg-indigo-50 dark:bg-indigo-950/40 px-2 py-1 text-xs font-medium text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/50 hover:bg-indigo-100 transition-colors"
                                    >
                                        <img 
                                            src="{{ asset('storage/img/platform/' . Str::lower($profile->platform->name) . '.png') }}" 
                                            alt="{{ $profile->platform->name }}"
                                            class="h-3 w-3 rounded-full object-contain error-fallback"
                                            onerror="this.style.display='none'"
                                        />
                                        <span>{{ $profile->platform->name }}</span>
                                    </a>
                                @empty
                                    <span class="text-xs text-gray-400">{{ __('No profiles') }}</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                            {{ number_format($influencer->socialProfiles->sum('followers_count')) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                            {{ number_format($influencer->socialProfiles->where('platform.name', 'Reddit')->sum('total_karma')) }}
                        </td>
                    </tr>
                @endforeach
            </x-ui::data-table>
        </x-ui::card>
    </div>
</x-layouts.app>