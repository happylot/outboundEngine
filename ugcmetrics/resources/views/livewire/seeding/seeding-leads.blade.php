<div class="flex h-full w-full flex-1 flex-col gap-6 p-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Mini Leads CRM') }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('Manage leads captured during seeding activities and track sync status with Zenify.cx CRM.') }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <x-ui::button 
                variant="primary" 
                :href="route('seeding.workspace')" 
                icon="clipboard-document-list"
            >
                {{ __('Tester Workspace') }}
            </x-ui::button>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session()->has('sync_success'))
        <x-ui::alert type="success" :message="session('sync_success')" />
    @endif
    @if(session()->has('sync_error'))
        <x-ui::alert type="danger" :message="session('sync_error')" />
    @endif

    <!-- Filters Card -->
    <x-ui::card :bordered="true">
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
            <!-- Search -->
            <div class="sm:col-span-6">
                <label for="search" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">{{ __('Search Lead') }}</label>
                <div class="relative rounded-md shadow-sm">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        id="search" 
                        placeholder="Search by name, phone, email, content..." 
                        class="block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5"
                    />
                </div>
            </div>

            <!-- Platform Filter -->
            <div class="sm:col-span-3">
                <label for="filterPlatform" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">{{ __('Platform') }}</label>
                <select 
                    wire:model.live="filterPlatform" 
                    id="filterPlatform" 
                    class="block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5"
                >
                    <option value="all">{{ __('All Platforms') }}</option>
                    <option value="facebook">{{ __('Facebook') }}</option>
                    <option value="instagram">{{ __('Instagram') }}</option>
                    <option value="threads">{{ __('Threads') }}</option>
                    <option value="reddit">{{ __('Reddit') }}</option>
                    <option value="tiktok">{{ __('TikTok') }}</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div class="sm:col-span-3">
                <label for="filterStatus" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">{{ __('Sync Status') }}</label>
                <select 
                    wire:model.live="filterStatus" 
                    id="filterStatus" 
                    class="block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5"
                >
                    <option value="all">{{ __('All Statuses') }}</option>
                    <option value="new">{{ __('New') }}</option>
                    <option value="syncing">{{ __('Syncing') }}</option>
                    <option value="synced">{{ __('Synced') }}</option>
                    <option value="failed">{{ __('Failed') }}</option>
                </select>
            </div>
        </div>
    </x-ui::card>

    <!-- Leads Table Card -->
    <x-ui::card :bordered="true" class="overflow-hidden">
        <div class="overflow-x-auto -mx-6">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800/55">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('ID') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Customer') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Platform') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Interacted Content') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Seeder Account') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Zenify Sync') }}</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                    @forelse($leads as $lead)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">
                            <!-- ID -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-500 dark:text-gray-400">
                                #{{ $lead->id }}
                            </td>
                            <!-- Customer Details -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                        {{ $lead->customer_name }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        @if($lead->customer_uid)
                                            UID: {{ $lead->customer_uid }}
                                        @endif
                                        @if($lead->phone)
                                            • Tel: {{ $lead->phone }}
                                        @endif
                                        @if($lead->email)
                                            • {{ $lead->email }}
                                        @endif
                                    </span>
                                    @if($lead->profile_url)
                                        <a href="{{ $lead->profile_url }}" target="_blank" class="text-[11px] text-indigo-500 dark:text-indigo-400 hover:underline mt-0.5 flex items-center gap-1">
                                            <span>{{ __('Profile Link') }}</span>
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <!-- Platform -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold uppercase tracking-wider
                                    @if($lead->platform === 'facebook') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                    @elseif($lead->platform === 'instagram') bg-pink-100 text-pink-800 dark:bg-pink-900/30 dark:text-pink-400
                                    @elseif($lead->platform === 'threads') bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300
                                    @elseif($lead->platform === 'reddit') bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400
                                    @elseif($lead->platform === 'tiktok') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400
                                    @endif
                                ">
                                    {{ $lead->platform }}
                                </span>
                            </td>
                            <!-- Interacted Content -->
                            <td class="px-6 py-4">
                                <div class="max-w-xs sm:max-w-sm md:max-w-md">
                                    <p class="text-xs text-gray-700 dark:text-gray-300 line-clamp-2 leading-relaxed" title="{{ $lead->content }}">
                                        {{ $lead->content ?: __('No content captured') }}
                                    </p>
                                    @if($lead->comment_url)
                                        <a href="{{ $lead->comment_url }}" target="_blank" class="text-[10px] text-gray-400 hover:text-indigo-500 hover:underline mt-1 flex items-center gap-0.5">
                                            <span>{{ __('Target Post/Comment URL') }}</span>
                                            <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <!-- Seeder Account -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold text-gray-800 dark:text-gray-300">
                                @if($lead->seederAccount)
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-900 dark:text-white">{{ $lead->seederAccount->username }}</span>
                                        <span class="text-gray-400 text-[10px] uppercase font-medium">{{ $lead->seederAccount->platform }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">{{ __('N/A') }}</span>
                                @endif
                            </td>
                            <!-- Sync Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    @if($lead->status === 'synced')
                                        <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                            {{ __('Synced') }}
                                        </span>
                                        @if($lead->zenify_lead_id)
                                            <span class="text-[9px] text-gray-400 uppercase tracking-tight">ID: {{ $lead->zenify_lead_id }}</span>
                                        @endif
                                    @elseif($lead->status === 'syncing')
                                        <span class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 dark:text-blue-400 animate-pulse">
                                            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3 3L22 4" /></svg>
                                            {{ __('Syncing...') }}
                                        </span>
                                    @elseif($lead->status === 'failed')
                                        <span class="inline-flex items-center gap-1 text-xs font-bold text-red-600 dark:text-red-400" title="{{ $lead->sync_error }}">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                            {{ __('Failed') }}
                                        </span>
                                        <span class="text-[9px] text-red-500 max-w-[120px] truncate" title="{{ $lead->sync_error }}">{{ $lead->sync_error }}</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 dark:text-amber-400">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            {{ __('New') }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                @if($lead->status === 'failed' || $lead->status === 'new')
                                    <button 
                                        wire:click="syncLead({{ $lead->id }})" 
                                        class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 bg-indigo-50 dark:bg-indigo-950/40 px-2 py-1 rounded transition-colors cursor-pointer"
                                        title="{{ __('Retry Sync') }}"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3 3L22 4" /></svg>
                                        {{ __('Retry') }}
                                    </button>
                                @endif

                                <button 
                                    onclick="confirm('Are you sure you want to delete this lead record?') || event.stopImmediatePropagation()"
                                    wire:click="deleteLead({{ $lead->id }})" 
                                    class="inline-flex items-center gap-1 text-xs font-semibold text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 bg-red-50 dark:bg-red-950/40 px-2 py-1 rounded transition-colors cursor-pointer"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    {{ __('Delete') }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 002-2h2a2 2 0 002 2" /></svg>
                                    <span>{{ __('No leads captured matching your filters.') }}</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($leads->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800">
                {{ $leads->links() }}
            </div>
        @endif
    </x-ui::card>
</div>
