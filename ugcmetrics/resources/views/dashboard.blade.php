<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-4">
        <!-- Top Stats Row -->
        <div class="grid gap-6 md:grid-cols-3">
            <x-ui::stat-card
                :value="\App\Models\Influencer::count()"
                :label="__('Total Influencers')"
                variant="primary"
                icon="users"
                :href="route('influencer.index')"
            />

            <x-ui::stat-card
                :value="\App\Models\SocialProfile::count()"
                :label="__('Social Profiles')"
                variant="success"
                icon="globe"
            />

            <x-ui::stat-card
                :value="\App\Models\Platform::count()"
                :label="__('Supported Platforms')"
                variant="info"
                icon="adjustments-horizontal"
            />
        </div>

        <!-- Main Dashboard Content Grid -->
        <div class="grid gap-6 md:grid-cols-2">
            <!-- Seeding Marketing Introduction Card -->
            <x-ui::card
                :title="__('Seeding Marketing Campaign Control')"
                :subtitle="__('Manage, test and monitor marketing seeding accounts and thresholds.')"
                :bordered="true"
                padding="lg"
            >
                <div class="space-y-4">
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Welcome to the Seeding Control Hub. Run A/B tests to find safe interaction frequencies for Meta (Facebook, Instagram, Threads), Reddit, and TikTok without triggering checkpoints or bans.
                    </p>
                    <div class="flex flex-wrap gap-3 pt-2">
                        <x-ui::button
                            variant="primary"
                            :href="route('seeding.workspace')"
                            icon="clipboard-document-list"
                        >
                            {{ __('Open Tester Workspace') }}
                        </x-ui::button>
                        
                        <x-ui::button
                            variant="secondary"
                            :href="route('seeding.dashboard')"
                            icon="chart-bar"
                        >
                            {{ __('Seeding Analytics') }}
                        </x-ui::button>
                    </div>
                </div>
            </x-ui::card>

            <!-- Latest Influencers Card -->
            <x-ui::card
                :title="__('Recent Influencers')"
                :subtitle="__('Quick overview of the latest added creators.')"
                :bordered="true"
                padding="lg"
            >
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse(\App\Models\Influencer::latest()->take(4)->get() as $influencer)
                        <div class="flex items-center justify-between py-3 first:pt-0 last:pb-0">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 shrink-0 overflow-hidden rounded-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                                    <img 
                                        src="{{ $influencer->profile_picture_url ? asset('storage/' . $influencer->profile_picture_url) : asset('storage/img/influencer/placeholder-profile.png') }}" 
                                        alt="{{ $influencer->name }}"
                                        class="h-full w-full object-cover"
                                    />
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $influencer->name }}</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">@ {{ $influencer->username ?? $influencer->name }}</p>
                                </div>
                            </div>
                            <x-ui::button 
                                variant="secondary" 
                                size="sm" 
                                :href="route('influencer.index')"
                            >
                                {{ __('View') }}
                            </x-ui::button>
                        </div>
                    @empty
                        <p class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('No influencers found.') }}
                        </p>
                    @endforelse
                </div>
            </x-ui::card>
        </div>
    </div>
</x-layouts.app>
