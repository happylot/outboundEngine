<x-layouts.app :title="__('Instagram Metrics')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-4">
        <!-- Top Stats Row -->
        <div class="grid gap-6 md:grid-cols-3">
            <x-ui::stat-card
                :value="$influencers->count()"
                :label="__('Instagram Influencers')"
                variant="primary"
                icon="users"
            />
            
            <x-ui::stat-card
                :value="$influencers->flatMap->socialProfiles->where('platform.name', 'Instagram')->sum('followers_count')"
                :label="__('Total Instagram Followers')"
                variant="success"
                icon="globe"
            />

            <x-ui::stat-card
                :value="$influencers->flatMap->socialProfiles->where('platform.name', 'Instagram')->count()"
                :label="__('Active Instagram Accounts')"
                variant="info"
                icon="check-circle"
            />
        </div>

        <!-- Table Listing Card -->
        <x-ui::card
            :title="__('Instagram Metrics Analytics')"
            :subtitle="__('Overview of influencers with connected Instagram profiles and follower statistics.')"
            :bordered="true"
            padding="lg"
        >
            <x-ui::data-table
                :headers="[
                    'photo' => __('Photo'),
                    'name' => __('Name'),
                    'instagram_username' => __('Instagram Profile'),
                    'followers' => __('Followers Count')
                ]"
                :searchable="true"
                :sortable="false"
                :striped="true"
                :hoverable="true"
                emptyMessage="No Instagram influencers found."
            >
                @foreach ($influencers as $influencer)
                    @php
                        $instagramProfile = $influencer->socialProfiles->where('platform.name', 'Instagram')->first();
                    @endphp
                    @if($instagramProfile)
                        <tr data-searchable>
                            <td class="px-4 py-3">
                                <div class="h-12 w-12 overflow-hidden rounded-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                                    <a href="{{ route('instagram.show', $influencer->id) }}">
                                        <img 
                                            src="{{ $influencer->profile_picture_url ? asset('storage/' . $influencer->profile_picture_url) : asset('storage/img/influencer/placeholder-profile.png') }}" 
                                            alt="Foto de {{ $influencer->name }}"
                                            class="h-full w-full object-cover"
                                        />
                                    </a>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">
                                {{ $influencer->name }}
                            </td>
                            <td class="px-4 py-3">
                                <a 
                                    href="{{ $instagramProfile->profile_url ?? '#' }}" 
                                    target="_blank"
                                    class="inline-flex items-center gap-1.5 text-indigo-600 dark:text-indigo-400 hover:underline font-medium text-sm"
                                >
                                    <img 
                                        src="{{ asset('storage/img/platform/instagram.png') }}" 
                                        alt="Instagram"
                                        class="h-4.5 w-4.5 object-contain"
                                        onerror="this.style.display='none'"
                                    />
                                    <span>@ {{ $instagramProfile->username }}</span>
                                </a>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 font-semibold">
                                {{ number_format($instagramProfile->followers_count) }}
                            </td>
                        </tr>
                    @endif
                @endforeach
            </x-ui::data-table>
        </x-ui::card>
    </div>
</x-layouts.app>
