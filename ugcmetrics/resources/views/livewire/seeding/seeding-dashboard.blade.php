<div class="flex h-full w-full flex-1 flex-col gap-6 p-4">
    <!-- Header -->
    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Seeding Marketing Hub') }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('R&D control center for account testing, safety thresholds, and daily seeding operations.') }}
            </p>
        </div>
        <div>
            <x-ui::button 
                variant="primary" 
                :href="route('seeding.workspace')" 
                icon="clipboard-document-list"
            >
                {{ __('Go to Tester Workspace') }}
            </x-ui::button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-gray-200 dark:border-gray-700">
        <button 
            wire:click="setTab('stats')" 
            class="py-2.5 px-4 text-sm font-semibold border-b-2 transition-colors {{ $activeTab === 'stats' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}"
        >
            <span class="inline-flex items-center gap-1.5">
                <x-ui::icon name="chart-bar" size="sm" />
                {{ __('Stats & Analytics') }}
            </span>
        </button>
        <button 
            wire:click="setTab('accounts')" 
            class="py-2.5 px-4 text-sm font-semibold border-b-2 transition-colors {{ $activeTab === 'accounts' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}"
        >
            <span class="inline-flex items-center gap-1.5">
                <x-ui::icon name="users" size="sm" />
                {{ __('Accounts & Devices') }}
            </span>
        </button>
        <button 
            wire:click="setTab('behaviors')" 
            class="py-2.5 px-4 text-sm font-semibold border-b-2 transition-colors {{ $activeTab === 'behaviors' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}"
        >
            <span class="inline-flex items-center gap-1.5">
                <x-ui::icon name="adjustments-horizontal" size="sm" />
                {{ __('Behavior Testing Groups') }}
            </span>
        </button>
        <button 
            wire:click="setTab('incidents')" 
            class="py-2.5 px-4 text-sm font-semibold border-b-2 transition-colors {{ $activeTab === 'incidents' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}"
        >
            <span class="inline-flex items-center gap-1.5">
                <x-ui::icon name="exclamation-triangle" size="sm" />
                {{ __('Checkpoint & Incidents') }}
                @if($checkpointAccounts > 0)
                    <span class="ml-1 rounded-full bg-rose-100 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 px-2 py-0.5 text-xs font-bold border border-rose-200 dark:border-rose-900/50">
                        {{ $checkpointAccounts }}
                    </span>
                @endif
            </span>
        </button>
    </div>

    <!-- Active Tab Content -->
    @if ($activeTab === 'stats')
        <!-- Row 1: Stat Cards -->
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <x-ui::stat-card
                :value="$activeAccounts . ' / ' . $totalAccounts"
                :label="__('Active Accounts')"
                variant="primary"
                icon="users"
            />
            <x-ui::stat-card
                :value="$cooldownAccounts"
                :label="__('Accounts Cooling Down')"
                variant="info"
                icon="clock"
            />
            <x-ui::stat-card
                :value="$checkpointAccounts"
                :label="__('Checkpoint Accounts (Blocked)')"
                variant="danger"
                icon="shield-exclamation"
            />
            <x-ui::stat-card
                :value="$successRate . '%'"
                :label="__('Success Action Rate')"
                variant="success"
                icon="check-circle"
            />
        </div>

        <!-- Row 2: Analytics Breakdown -->
        <div class="grid gap-6 md:grid-cols-2">
            <!-- Platform Breakdown -->
            <x-ui::card 
                :title="__('Incidents by Platform')" 
                :subtitle="__('Historical count of security events registered on each network.')"
                :bordered="true"
            >
                <div class="space-y-4">
                    @forelse(['facebook', 'instagram', 'threads', 'reddit', 'tiktok'] as $p)
                        @php $cnt = $incidentsByPlatform[$p] ?? 0; @endphp
                        <div>
                            <div class="flex justify-between text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                <span class="capitalize">{{ $p }}</span>
                                <span>{{ $cnt }} {{ __('incidents') }}</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-rose-500 h-2 rounded-full" style="width: {{ $totalAccounts > 0 ? min(100, ($cnt / $totalAccounts) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">{{ __('No incidents recorded.') }}</p>
                    @endforelse
                </div>
            </x-ui::card>

            <!-- Age Group Analysis -->
            <x-ui::card 
                :title="__('Incidents by Account Age')" 
                :subtitle="__('Correlation between account lifetime and checkpoint occurrence.')"
                :bordered="true"
            >
                <div class="space-y-4">
                    @forelse(['< 6 months', '6-12 months', '1-3 years', '> 3 years'] as $age)
                        @php $cnt = $incidentsByAgeGroup[$age] ?? 0; @endphp
                        <div>
                            <div class="flex justify-between text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                <span>{{ $age }}</span>
                                <span>{{ $cnt }} {{ __('incidents') }}</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-amber-500 h-2 rounded-full" style="width: {{ $totalAccounts > 0 ? min(100, ($cnt / $totalAccounts) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">{{ __('No age group logs.') }}</p>
                    @endforelse
                </div>
            </x-ui::card>
        </div>

        <!-- Row 3: Recent Activity Logs -->
        <x-ui::card
            :title="__('Recent Seeding Activities')"
            :subtitle="__('Latest actions submitted by human testers.')"
            :bordered="true"
        >
            <x-ui::data-table
                :headers="[
                    'time' => __('Time'),
                    'account' => __('Account'),
                    'platform' => __('Platform'),
                    'action' => __('Action'),
                    'content' => __('Content Sneak Peek'),
                    'status' => __('Status')
                ]"
                :searchable="false"
                :sortable="false"
                :striped="true"
                :hoverable="true"
                emptyMessage="No seeding logs yet."
            >
                @foreach($recentLogs as $log)
                    <tr>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            {{ $log->logged_at->diffForHumans() }}
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white text-sm">
                            {{ $log->seederAccount->username }}
                        </td>
                        <td class="px-4 py-3 text-sm capitalize">
                            {{ $log->seederAccount->platform }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span class="inline-flex items-center rounded-md bg-gray-50 dark:bg-gray-800 px-2 py-1 text-xs font-semibold text-gray-600 dark:text-gray-400 border border-gray-100 dark:border-gray-700">
                                {{ $log->action_type }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 max-w-xs truncate">
                            {{ $log->content ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($log->status === 'success')
                                <span class="inline-flex items-center rounded bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/50">
                                    {{ __('Success') }}
                                </span>
                            @elseif($log->status === 'warning')
                                <span class="inline-flex items-center rounded bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 text-xs font-semibold text-amber-700 dark:text-amber-400 border border-amber-100 dark:border-amber-900/50">
                                    {{ __('Warning') }}
                                </span>
                            @else
                                <span class="inline-flex items-center rounded bg-rose-50 dark:bg-rose-950/40 px-2 py-0.5 text-xs font-semibold text-rose-700 dark:text-rose-400 border border-rose-100 dark:border-rose-900/50">
                                    {{ __('Checkpoint') }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </x-ui::data-table>
        </x-ui::card>
    @endif

    @if ($activeTab === 'accounts')
        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Left 2 columns: Tables -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Seeder Accounts List -->
                <x-ui::card :title="__('Seeder Accounts')" :subtitle="__('Accounts active in testing maxtrix.')" :bordered="true">
                    @if (session()->has('account_success'))
                        <div class="mb-4">
                            <x-ui::alert type="success" :message="session('account_success')" />
                        </div>
                    @endif

                    <x-ui::data-table
                        :headers="[
                            'username' => __('Username'),
                            'platform' => __('Platform'),
                            'age' => __('Age Group'),
                            'device' => __('Device'),
                            'group' => __('Test Group'),
                            'status' => __('Status'),
                            'actions' => __('Quick Actions')
                        ]"
                        :searchable="true"
                        :sortable="false"
                        :hoverable="true"
                        emptyMessage="No accounts created yet."
                    >
                        @foreach($accounts as $acc)
                            <tr data-searchable>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $acc->username }}
                                </td>
                                <td class="px-4 py-3 text-sm capitalize">
                                    {{ $acc->platform }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $acc->age_group }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $acc->device ? $acc->device->device_name : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="font-medium text-indigo-600 dark:text-indigo-400">
                                        {{ $acc->behaviorGroup ? $acc->behaviorGroup->name : '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($acc->status === 'active')
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 dark:bg-emerald-950/40 px-2 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/50">
                                            {{ __('Active') }}
                                        </span>
                                    @elseif($acc->status === 'cooldown')
                                        <span class="inline-flex items-center rounded-full bg-blue-50 dark:bg-blue-950/40 px-2 py-1 text-xs font-semibold text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-900/50" title="Until {{ $acc->cooldown_until ? $acc->cooldown_until->format('H:i') : '' }}">
                                            {{ __('Cooldown') }}
                                        </span>
                                    @elseif($acc->status === 'checkpoint')
                                        <span class="inline-flex items-center rounded-full bg-rose-50 dark:bg-rose-950/40 px-2 py-1 text-xs font-semibold text-rose-700 dark:text-rose-400 border border-rose-100 dark:border-rose-900/50">
                                            {{ __('Checkpoint') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-800 px-2 py-1 text-xs font-semibold text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700">
                                            {{ $acc->status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm space-x-1">
                                    @if($acc->status !== 'active')
                                        <x-ui::button 
                                            variant="secondary" 
                                            size="sm" 
                                            wire:click="changeAccountStatus({{ $acc->id }}, 'active')"
                                        >
                                            {{ __('Activate') }}
                                        </x-ui::button>
                                    @endif
                                    @if($acc->status !== 'checkpoint')
                                        <x-ui::button 
                                            variant="danger" 
                                            size="sm" 
                                            wire:click="changeAccountStatus({{ $acc->id }}, 'checkpoint')"
                                        >
                                            {{ __('Block') }}
                                        </x-ui::button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </x-ui::data-table>
                </x-ui::card>

                <!-- Seeding Devices List -->
                <x-ui::card :title="__('Testing Devices & Proxies')" :subtitle="__('Physical smartphones or VMs configured with residential proxy IPs.')" :bordered="true">
                    @if (session()->has('device_success'))
                        <div class="mb-4">
                            <x-ui::alert type="success" :message="session('device_success')" />
                        </div>
                    @endif

                    <x-ui::data-table
                        :headers="[
                            'name' => __('Device Name'),
                            'model' => __('Model / OS'),
                            'network' => __('Connection'),
                            'proxy' => __('Proxy IP'),
                            'status' => __('Status')
                        ]"
                        :searchable="false"
                        :sortable="false"
                        emptyMessage="No devices added yet."
                    >
                        @foreach($devices as $dev)
                            <tr>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $dev->device_name }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $dev->device_model }} ({{ $dev->os_version }})
                                </td>
                                <td class="px-4 py-3 text-sm capitalize">
                                    {{ $dev->connection_type }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 font-mono">
                                    {{ $dev->proxy_info ?? __('Direct Network') }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/50">
                                        {{ $dev->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </x-ui::data-table>
                </x-ui::card>
            </div>

            <!-- Right Column: Registration forms -->
            <div class="space-y-6">
                <!-- Add Account Form -->
                <x-ui::card :title="__('Create Seeder Account')" :bordered="true">
                    <form wire:submit.prevent="createAccount" class="space-y-4">
                        <x-ui::input 
                            wire:model="account_username" 
                            name="account_username" 
                            id="account_username" 
                            :label="__('Username')" 
                            :required="true"
                            placeholder="Enter username" 
                        />
                        
                        <x-ui::select 
                            wire:model="account_platform" 
                            name="account_platform" 
                            id="account_platform" 
                            :label="__('Platform')"
                            :required="true"
                        >
                            <option value="facebook">Facebook</option>
                            <option value="instagram">Instagram</option>
                            <option value="threads">Threads</option>
                            <option value="reddit">Reddit</option>
                            <option value="tiktok">TikTok</option>
                        </x-ui::select>

                        <x-ui::input 
                            wire:model="account_profile_url" 
                            name="account_profile_url" 
                            id="account_profile_url" 
                            :label="__('Profile URL')" 
                            placeholder="https://..." 
                        />

                        <x-ui::select 
                            wire:model="account_age_group" 
                            name="account_age_group" 
                            id="account_age_group" 
                            :label="__('Account Age Group')"
                            :required="true"
                        >
                            <option value="< 6 months">&lt; 6 months</option>
                            <option value="6-12 months">6-12 months</option>
                            <option value="1-3 years">1-3 years</option>
                            <option value="> 3 years">&gt; 3 years</option>
                        </x-ui::select>

                        <x-ui::select 
                            wire:model="account_device_id" 
                            name="account_device_id" 
                            id="account_device_id" 
                            :label="__('Associated Device')"
                        >
                            <option value="">{{ __('None (Direct PC)') }}</option>
                            @foreach($devices as $dev)
                                <option value="{{ $dev->id }}">{{ $dev->device_name }}</option>
                            @endforeach
                        </x-ui::select>

                        <x-ui::select 
                            wire:model="account_behavior_group_id" 
                            name="account_behavior_group_id" 
                            id="account_behavior_group_id" 
                            :label="__('Behavior Rules (A/B Test)')"
                        >
                            <option value="">{{ __('No rules (No cooldown checks)') }}</option>
                            @foreach($behaviorGroups as $bg)
                                <option value="{{ $bg->id }}">{{ $bg->name }}</option>
                            @endforeach
                        </x-ui::select>

                        <button type="submit" class="w-full inline-flex items-center justify-center font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-600 dark:hover:bg-blue-700 px-4 py-2.5 text-sm cursor-pointer shadow-sm">
                            {{ __('Save Account') }}
                        </button>
                    </form>
                </x-ui::card>

                <!-- Add Device Form -->
                <x-ui::card :title="__('Register Device')" :bordered="true">
                    <form wire:submit.prevent="createDevice" class="space-y-4">
                        <x-ui::input 
                            wire:model="device_name" 
                            name="device_name" 
                            id="device_name" 
                            :label="__('Device Name')" 
                            :required="true"
                            placeholder="e.g. iPhone 11 Pro - 01" 
                        />
                        <x-ui::input 
                            wire:model="device_model" 
                            name="device_model" 
                            id="device_model" 
                            :label="__('Model / Code')" 
                            :required="true"
                            placeholder="e.g. A2111" 
                        />
                        <x-ui::input 
                            wire:model="os_version" 
                            name="os_version" 
                            id="os_version" 
                            :label="__('OS Version')" 
                            :required="true"
                            placeholder="e.g. iOS 15.4" 
                        />
                        <x-ui::select 
                            wire:model="connection_type" 
                            name="connection_type" 
                            id="connection_type" 
                            :label="__('Connection Type')"
                            :required="true"
                        >
                            <option value="Wi-Fi">Wi-Fi</option>
                            <option value="4G">4G LTE (Cellular)</option>
                            <option value="Residential Proxy">Residential Proxy</option>
                        </x-ui::select>
                        <x-ui::input 
                            wire:model="proxy_info" 
                            name="proxy_info" 
                            id="proxy_info" 
                            :label="__('Proxy Address')" 
                            placeholder="IP:Port:User:Pass" 
                        />

                        <button type="submit" class="w-full inline-flex items-center justify-center font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-600 dark:hover:bg-blue-700 px-4 py-2.5 text-sm cursor-pointer shadow-sm">
                            {{ __('Register Device') }}
                        </button>
                    </form>
                </x-ui::card>
            </div>
        </div>
    @endif

    @if ($activeTab === 'behaviors')
        <div class="grid gap-6 lg:grid-cols-3">
            <!-- List -->
            <div class="lg:col-span-2">
                <x-ui::card :title="__('Behavior Testing Rules')" :subtitle="__('Configured daily action thresholds and random cooldown interval limits.')" :bordered="true">
                    @if (session()->has('group_success'))
                        <div class="mb-4">
                            <x-ui::alert type="success" :message="session('group_success')" />
                        </div>
                    @endif

                    <x-ui::data-table
                        :headers="[
                            'name' => __('Group Name'),
                            'limit' => __('Daily Limit'),
                            'min_cool' => __('Min Cooldown'),
                            'max_cool' => __('Max Cooldown'),
                            'description' => __('Description')
                        ]"
                        :searchable="false"
                        :sortable="false"
                        emptyMessage="No rules created yet."
                    >
                        @foreach($behaviorGroups as $bg)
                            <tr>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $bg->name }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 font-bold">
                                    {{ $bg->daily_limit }} {{ __('actions/day') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $bg->min_interval_minutes }} {{ __('minutes') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $bg->max_interval_minutes }} {{ __('minutes') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $bg->description ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </x-ui::data-table>
                </x-ui::card>
            </div>

            <!-- Form -->
            <x-ui::card :title="__('Create Behavior Group')" :bordered="true">
                <form wire:submit.prevent="createBehaviorGroup" class="space-y-4">
                    <x-ui::input 
                        wire:model="group_name" 
                        name="group_name" 
                        id="group_name" 
                        :label="__('Group Name')" 
                        :required="true"
                        placeholder="e.g. Nhóm 1 - Giãn cách 4h" 
                    />
                    <x-ui::input 
                        wire:model="daily_limit" 
                        type="number"
                        name="daily_limit" 
                        id="daily_limit" 
                        :label="__('Daily Action Limit')" 
                        :required="true"
                    />
                    <x-ui::input 
                        wire:model="min_interval_minutes" 
                        type="number"
                        name="min_interval_minutes" 
                        id="min_interval_minutes" 
                        :label="__('Min Cooldown (Minutes)')" 
                        :required="true"
                    />
                    <x-ui::input 
                        wire:model="max_interval_minutes" 
                        type="number"
                        name="max_interval_minutes" 
                        id="max_interval_minutes" 
                        :label="__('Max Cooldown (Minutes)')" 
                        :required="true"
                    />
                    <x-ui::textarea 
                        wire:model="group_description" 
                        name="group_description" 
                        id="group_description" 
                        :label="__('Description')" 
                    />

                    <button type="submit" class="w-full inline-flex items-center justify-center font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-600 dark:hover:bg-blue-700 px-4 py-2.5 text-sm cursor-pointer shadow-sm">
                        {{ __('Save Behavior Group') }}
                    </button>
                </form>
            </x-ui::card>
        </div>
    @endif

    @if ($activeTab === 'incidents')
        <x-ui::card
            :title="__('Incidents History Log')"
            :subtitle="__('Audit trail of triggered checkpoints, shadowbans or blocks.')"
            :bordered="true"
        >
            <x-ui::data-table
                :headers="[
                    'time' => __('Reported At'),
                    'account' => __('Account'),
                    'platform' => __('Platform'),
                    'type' => __('Incident Type'),
                    'trigger' => __('Trigger Action'),
                    'desc' => __('Description'),
                    'status' => __('Status')
                ]"
                :searchable="false"
                :sortable="false"
                :striped="true"
                emptyMessage="No incidents recorded. Accounts are running safe!"
            >
                @foreach($incidents as $inc)
                    <tr>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            {{ $inc->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white text-sm">
                            {{ $inc->seederAccount->username }}
                        </td>
                        <td class="px-4 py-3 text-sm capitalize">
                            {{ $inc->seederAccount->platform }}
                        </td>
                        <td class="px-4 py-3 text-sm font-semibold text-rose-600 dark:text-rose-400">
                            {{ $inc->incident_type }}
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            @if($inc->triggerAction)
                                <span class="font-bold font-mono">{{ $inc->triggerAction->action_type }}</span>
                                <span class="block truncate max-w-xs">{{ $inc->triggerAction->content }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            {{ $inc->description ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($inc->resolved_at)
                                <span class="inline-flex items-center rounded bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/50">
                                    {{ __('Resolved') }} ({{ $inc->recovery_time_minutes }}m)
                                </span>
                            @else
                                <span class="inline-flex items-center rounded bg-rose-50 dark:bg-rose-950/40 px-2 py-0.5 text-xs font-semibold text-rose-700 dark:text-rose-400 border border-rose-100 dark:border-rose-900/50">
                                    {{ __('Active Block') }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </x-ui::data-table>

            <div class="mt-4">
                {{ $incidents->links() }}
            </div>
        </x-ui::card>
    @endif
</div>
