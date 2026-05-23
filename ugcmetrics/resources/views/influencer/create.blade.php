<x-layouts.app :title="__('Create Influencer')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-4 max-w-4xl mx-auto">
        <x-ui::card
            :title="__('Create Influencer Profile')"
            :subtitle="__('Add a new content creator and link their social profiles.')"
            :bordered="true"
            padding="lg"
        >
            <form action="{{ route('influencer.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <!-- Creator Name Input -->
                <x-ui::input 
                    name="name" 
                    id="name" 
                    :label="__('Full Name')" 
                    :required="true"
                    :placeholder="__('Enter creator\'s full name')" 
                />

                <!-- Profile Picture Input -->
                <div class="space-y-1">
                    <label for="profile_picture" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('Profile Picture') }}
                    </label>
                    <input 
                        type="file" 
                        name="profile_picture" 
                        id="profile_picture" 
                        accept="image/*" 
                        class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-950/40 dark:file:text-indigo-400 hover:file:bg-indigo-100 transition-colors"
                    />
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('Social Media Profiles') }}
                    </h3>

                    <!-- Initial Social Profile Group -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-800/30 rounded-lg border border-gray-100 dark:border-gray-800/60 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-ui::input 
                                name="social_username" 
                                id="social_username" 
                                :label="__('Username')" 
                                :placeholder="__('Enter username')" 
                            />
                            
                            <x-ui::select 
                                name="platform_id" 
                                id="platform_id" 
                                :label="__('Platform')" 
                                :placeholder="__('Select platform')"
                            >
                                @foreach($platforms as $platform)
                                    <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                                @endforeach
                            </x-ui::select>
                        </div>

                        <div class="pt-2">
                            <x-ui::button 
                                type="button" 
                                id="addMoreProfiles" 
                                variant="secondary" 
                                size="sm" 
                                icon="plus"
                            >
                                {{ __('Add Another Profile') }}
                            </x-ui::button>
                        </div>

                        <!-- Dynamic Additional Profiles -->
                        <div id="additionalProfiles" class="space-y-4"></div>
                    </div>
                </div>

                <!-- Submit and Back Actions -->
                <div class="flex items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <x-ui::button type="submit" variant="primary">
                        {{ __('Create Influencer') }}
                    </x-ui::button>

                    <x-ui::button variant="secondary" :href="route('influencer.index')">
                        {{ __('Cancel') }}
                    </x-ui::button>
                </div>
            </form>
        </x-ui::card>
    </div>

    <!-- Script for handling dynamic profiles generation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let profileCount = 1;
            
            document.getElementById('addMoreProfiles').addEventListener('click', function() {
                const profilesContainer = document.getElementById('additionalProfiles');
                const newProfileDiv = document.createElement('div');
                newProfileDiv.className = 'grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700/60 relative';
                newProfileDiv.innerHTML = `
                    <div>
                        <label for="social_username_${profileCount}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Username') }}
                        </label>
                        <input 
                            type="text" 
                            name="social_usernames[]" 
                            id="social_username_${profileCount}" 
                            class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 transition-colors" 
                            placeholder="{{ __('Enter username') }}"
                        />
                    </div>
                    <div class="flex flex-col justify-between">
                        <div>
                            <label for="platform_id_${profileCount}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('Platform') }}
                            </label>
                            <select 
                                name="platform_ids[]" 
                                id="platform_id_${profileCount}" 
                                class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 transition-colors"
                            >
                                <option value="" disabled selected>{{ __('Select platform') }}</option>
                                @foreach($platforms as $platform)
                                    <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button 
                            type="button" 
                            class="remove-profile inline-flex items-center gap-1 text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 mt-2 font-semibold self-end"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            {{ __('Remove Profile') }}
                        </button>
                    </div>
                `;
                
                profilesContainer.appendChild(newProfileDiv);
                
                newProfileDiv.querySelector('.remove-profile').addEventListener('click', function() {
                    profilesContainer.removeChild(newProfileDiv);
                });
                
                profileCount++;
            });
        });
    </script>
</x-layouts.app>
