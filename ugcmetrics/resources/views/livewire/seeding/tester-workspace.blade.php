<div class="flex h-full w-full flex-1 flex-col gap-6 p-4">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Tester R&D Workspace') }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ __('Perform manual seeding tasks on test devices, and log results here to compute cooldown safety thresholds.') }}
        </p>
    </div>

    <!-- Main Workspace Layout -->
    <div class="grid gap-6 md:grid-cols-3 flex-1 items-start">
        
        <!-- Left Column: Accounts Selector List (1/3) -->
        <x-ui::card :title="__('Select Test Account')" :subtitle="__('Select the account currently active on your test device.')" :bordered="true" class="md:col-span-1">
            <div class="space-y-3 max-h-[600px] overflow-y-auto pr-1">
                @forelse($accounts as $acc)
                    <button 
                        wire:click="selectAccount({{ $acc->id }})"
                        class="w-full text-left p-3 rounded-lg border transition-all flex items-center justify-between {{ $selectedAccountId === $acc->id ? 'border-indigo-600 bg-indigo-50/50 dark:border-indigo-500 dark:bg-indigo-950/20 ring-1 ring-indigo-500' : 'border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600 bg-white dark:bg-gray-800' }}"
                    >
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full border bg-gray-50 dark:bg-gray-700 flex items-center justify-center font-bold text-xs uppercase text-gray-500 dark:text-gray-400">
                                {{ substr($acc->platform, 0, 2) }}
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $acc->username }}</h4>
                                <span class="text-xs text-gray-500 capitalize">{{ $acc->platform }} • {{ $acc->age_group }}</span>
                            </div>
                        </div>

                        <!-- Status badge -->
                        <div>
                            @if($acc->status === 'active')
                                <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 inline-block" title="Active"></span>
                            @elseif($acc->status === 'cooldown')
                                <span class="h-2.5 w-2.5 rounded-full bg-blue-500 inline-block" title="Cooldown"></span>
                            @elseif($acc->status === 'checkpoint')
                                <span class="h-2.5 w-2.5 rounded-full bg-rose-500 inline-block" title="Checkpoint"></span>
                            @else
                                <span class="h-2.5 w-2.5 rounded-full bg-gray-400 inline-block"></span>
                            @endif
                        </div>
                    </button>
                @empty
                    <p class="text-center text-sm text-gray-500 dark:text-gray-400 py-6">
                        {{ __('No seeding accounts registered.') }}
                    </p>
                @endforelse
            </div>
        </x-ui::card>

        <!-- Right Column: Detail & Logging Form (2/3) -->
        <div class="md:col-span-2 space-y-6">
            @if(session()->has('log_success'))
                <x-ui::alert type="success" :message="session('log_success')" />
            @endif
            @if(session()->has('log_error'))
                <x-ui::alert type="danger" :message="session('log_error')" />
            @endif

            @if($selectedAccount)
                <!-- Selected Account Details & Status -->
                <x-ui::card :title="__('Active Account: ') . $selectedAccount->username" :bordered="true">
                    
                    <!-- Alert Banners depending on current state -->
                    <div class="mb-5 space-y-3">
                        @if($selectedAccount->status === 'checkpoint')
                            <div class="p-4 rounded-lg bg-rose-50 border border-rose-200 dark:bg-rose-950/20 dark:border-rose-900/50">
                                <div class="flex gap-2">
                                    <x-ui::icon name="shield-exclamation" class="text-rose-600 dark:text-rose-400" />
                                    <div>
                                        <h4 class="text-sm font-bold text-rose-800 dark:text-rose-400">{{ __('Checkpoint Active!') }}</h4>
                                        <p class="text-xs text-rose-700 dark:text-rose-300/80 mt-0.5">
                                            {{ __('This account was flagged or locked by the platform. You must solve the verification manually on the device before logging any further success.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @elseif($isCooldown)
                            <div class="p-4 rounded-lg bg-blue-50 border border-blue-200 dark:bg-blue-950/20 dark:border-blue-900/50">
                                <div class="flex gap-2">
                                    <x-ui::icon name="clock" class="text-blue-600 dark:text-blue-400" />
                                    <div>
                                        <h4 class="text-sm font-bold text-blue-800 dark:text-blue-400">
                                            {{ __('Safety Cooldown Active') }}
                                        </h4>
                                        <p class="text-xs text-blue-700 dark:text-blue-300/80 mt-0.5">
                                            {{ __('Under behavior group guidelines, this account should rest until') }} 
                                            <span class="font-bold">{{ $selectedAccount->cooldown_until->format('H:i') }}</span> 
                                            ({{ $selectedAccount->cooldown_until->diffForHumans() }}).
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @elseif($isLimitReached)
                            <div class="p-4 rounded-lg bg-amber-50 border border-amber-200 dark:bg-amber-950/20 dark:border-amber-900/50">
                                <div class="flex gap-2">
                                    <x-ui::icon name="exclamation-triangle" class="text-amber-600 dark:text-amber-400" />
                                    <div>
                                        <h4 class="text-sm font-bold text-amber-800 dark:text-amber-400">{{ __('Daily limit reached!') }}</h4>
                                        <p class="text-xs text-amber-700 dark:text-amber-300/80 mt-0.5">
                                            {{ __('This account has performed') }} <span class="font-bold">{{ $actionsToday }} / {{ $dailyLimit }}</span> {{ __('allowed interactions for today. Logging further actions is allowed but warning flags might increase.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-200 dark:bg-emerald-950/20 dark:border-emerald-900/50">
                                <div class="flex gap-2">
                                    <x-ui::icon name="check-circle" class="text-emerald-600 dark:text-emerald-400" />
                                    <div>
                                        <h4 class="text-sm font-bold text-emerald-800 dark:text-emerald-400">{{ __('Safe to Seed') }}</h4>
                                        <p class="text-xs text-emerald-700 dark:text-emerald-300/80 mt-0.5">
                                            {{ __('All cooldown parameters are clear. Account is ready to execute actions.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Meta specifications -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs bg-gray-50 dark:bg-gray-800/40 p-4 rounded-lg border border-gray-100 dark:border-gray-800/50">
                        <div>
                            <span class="text-gray-400 block mb-0.5">{{ __('Platform') }}</span>
                            <span class="font-semibold text-gray-900 dark:text-white capitalize">{{ $selectedAccount->platform }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block mb-0.5">{{ __('Device Mapping') }}</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $selectedAccount->device ? $selectedAccount->device->device_name : __('Direct PC (No Proxy)') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block mb-0.5">{{ __('A/B Group') }}</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $selectedAccount->behaviorGroup ? $selectedAccount->behaviorGroup->name : __('No testing group') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block mb-0.5">{{ __('Actions Today') }}</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $actionsToday }} {{ $dailyLimit ? '/ ' . $dailyLimit : '' }}</span>
                        </div>
                    </div>

                    <!-- Checkpoint Solving Form -->
                    @if($selectedAccount->status === 'checkpoint')
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 space-y-4">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Recover Account & Resolve Checkpoint') }}</h4>
                            <div class="space-y-4">
                                <x-ui::input 
                                    wire:model="checkpoint_resolve_notes" 
                                    name="checkpoint_resolve_notes" 
                                    id="checkpoint_resolve_notes" 
                                    :label="__('Resolution Action')" 
                                    placeholder="e.g. Uploaded self-ie, confirmed SMS code, changed password"
                                />
                                <x-ui::button 
                                    type="button" 
                                    variant="success" 
                                    wire:click="resolveCheckpoint"
                                    icon="check"
                                >
                                    {{ __('Mark as Resolved & Set Active') }}
                                </x-ui::button>
                            </div>
                        </div>
                    @endif
                </x-ui::card>

                <!-- Logging Workspace Form (Only enabled if account status is not Checkpoint) -->
                @if($selectedAccount->status !== 'checkpoint')
                    <x-ui::card :title="__('Submit Seeding Log')" :subtitle="__('After performing an action on the phone/browser, submit details to compute safety scores.')" :bordered="true">
                        <form wire:submit.prevent="submitLog" class="space-y-5">
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Action type -->
                                <x-ui::select 
                                    wire:model="action_type" 
                                    name="action_type" 
                                    id="action_type" 
                                    :label="__('Action Type')"
                                    :required="true"
                                >
                                    <option value="comment">{{ __('Comment (Bình luận)') }}</option>
                                    <option value="like">{{ __('Like / Upvote (Thích)') }}</option>
                                    <option value="post">{{ __('Create Post / Thread (Đăng bài)') }}</option>
                                    <option value="share">{{ __('Share / Repost (Chia sẻ)') }}</option>
                                    <option value="follow">{{ __('Follow / Join (Theo dõi)') }}</option>
                                </x-ui::select>

                                <!-- Content type -->
                                <x-ui::select 
                                    wire:model="content_type" 
                                    name="content_type" 
                                    id="content_type" 
                                    :label="__('Content Composition')"
                                    :required="true"
                                >
                                    <option value="hand-written-short">{{ __('Typed - Short Comment (< 15 words)') }}</option>
                                    <option value="hand-written-long">{{ __('Typed - Long Comment/Post (> 15 words)') }}</option>
                                    <option value="copy-paste">{{ __('Copy Paste (Sử dụng kịch bản mẫu)') }}</option>
                                    <option value="none">{{ __('None (Likes/Follows)') }}</option>
                                </x-ui::select>
                            </div>

                            <!-- Target URL -->
                            <x-ui::input 
                                wire:model="target_url" 
                                name="target_url" 
                                id="target_url" 
                                :label="__('Target Content URL')" 
                                placeholder="https://facebook.com/... or https://tiktok.com/..." 
                            />

                            <!-- Comment Content -->
                            @if(in_array($action_type, ['comment', 'post']))
                                <x-ui::textarea 
                                    wire:model="content" 
                                    name="content" 
                                    id="content" 
                                    :label="__('Interaction Content (Nội dung seeding)')" 
                                    :required="true"
                                    placeholder="Write or paste exactly what you posted..." 
                                />
                            @endif

                            <!-- Result status -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('Platform Feed-back / Result') }}
                                </label>
                                <div class="grid grid-cols-3 gap-3">
                                    <!-- Success -->
                                    <label class="cursor-pointer border rounded-lg p-3 flex flex-col items-center gap-1 transition-all {{ $log_status === 'success' ? 'border-emerald-500 bg-emerald-50/20 text-emerald-800 dark:text-emerald-400 dark:border-emerald-500' : 'border-gray-200 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300' }}">
                                        <input type="radio" wire:model="log_status" name="log_status" value="success" class="sr-only" />
                                        <x-ui::icon name="check-circle" size="md" />
                                        <span class="text-xs font-bold">{{ __('Success') }}</span>
                                    </label>
                                    
                                    <!-- Warning -->
                                    <label class="cursor-pointer border rounded-lg p-3 flex flex-col items-center gap-1 transition-all {{ $log_status === 'warning' ? 'border-amber-500 bg-amber-50/20 text-amber-800 dark:text-amber-400 dark:border-amber-500' : 'border-gray-200 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300' }}">
                                        <input type="radio" wire:model="log_status" name="log_status" value="warning" class="sr-only" />
                                        <x-ui::icon name="exclamation-circle" size="md" />
                                        <span class="text-xs font-bold">{{ __('Warning/Block') }}</span>
                                    </label>

                                    <!-- Checkpoint -->
                                    <label class="cursor-pointer border rounded-lg p-3 flex flex-col items-center gap-1 transition-all {{ $log_status === 'checkpoint_triggered' ? 'border-rose-500 bg-rose-50/20 text-rose-800 dark:text-rose-400 dark:border-rose-500' : 'border-gray-200 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300' }}">
                                        <input type="radio" wire:model="log_status" name="log_status" value="checkpoint_triggered" class="sr-only" />
                                        <x-ui::icon name="shield-exclamation" size="md" />
                                        <span class="text-xs font-bold">{{ __('Checkpoint!') }}</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Checkpoint Details Form -->
                            @if($log_status === 'checkpoint_triggered')
                                <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 space-y-3">
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ __('Checkpoint Details') }}</h4>
                                    <x-ui::textarea 
                                        wire:model="incident_description" 
                                        name="incident_description" 
                                        id="incident_description" 
                                        :label="__('Flag description')" 
                                        placeholder="e.g. Phone number confirmation, Photo match check, Account suspended." 
                                    />
                                </div>
                            @endif

                            <!-- Cooldown Warning Override notification -->
                            @if($isCooldown || $isLimitReached)
                                <div class="text-xs text-amber-600 dark:text-amber-400 font-semibold italic">
                                    * {{ __('Warning: You are overriding standard behavior rules. This is allowed for testing limits.') }}
                                </div>
                            @endif

                            <!-- Submit Button -->
                            <button type="submit" class="w-full inline-flex items-center justify-center font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-600 dark:hover:bg-blue-700 px-4 py-2.5 text-sm cursor-pointer shadow-sm">
                                {{ __('Log Interaction & Apply Cooldown') }}
                            </button>
                        </form>
                    </x-ui::card>
                @endif

                <!-- Log Potential Lead Form (Mini CRM integration) -->
                <div class="mt-6">
                    <x-ui::card :title="__('Log Potential Lead')" :subtitle="__('Capture interactions with leads on personal accounts or group comments and sync directly to Zenify.cx.')" :bordered="true">
                        @if(session()->has('lead_success'))
                            <div class="mb-4">
                                <x-ui::alert type="success" :message="session('lead_success')" />
                            </div>
                        @endif
                        @if(session()->has('lead_warning'))
                            <div class="mb-4">
                                <x-ui::alert type="warning" :message="session('lead_warning')" />
                            </div>
                        @endif

                        <form wire:submit.prevent="submitLead" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <x-ui::input 
                                    wire:model="lead_name" 
                                    name="lead_name" 
                                    id="lead_name" 
                                    :label="__('Customer Name (Tên khách hàng)')" 
                                    :required="true"
                                    placeholder="e.g. Nguyễn Văn B" 
                                />
                                <x-ui::input 
                                    wire:model="lead_uid" 
                                    name="lead_uid" 
                                    id="lead_uid" 
                                    :label="__('Customer Username/UID')" 
                                    placeholder="e.g. vanb.nguyen.12" 
                                />
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <x-ui::input 
                                    wire:model="lead_phone" 
                                    name="lead_phone" 
                                    id="lead_phone" 
                                    :label="__('Phone Number')" 
                                    placeholder="e.g. 0912345678" 
                                />
                                <x-ui::input 
                                    wire:model="lead_email" 
                                    name="lead_email" 
                                    id="lead_email" 
                                    :label="__('Email Address')" 
                                    type="email"
                                    placeholder="e.g. vanb@gmail.com" 
                                />
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <x-ui::input 
                                    wire:model="lead_profile_url" 
                                    name="lead_profile_url" 
                                    id="lead_profile_url" 
                                    :label="__('Customer Profile URL')" 
                                    placeholder="https://facebook.com/profile.php?id=..." 
                                />
                                <x-ui::input 
                                    wire:model="lead_comment_url" 
                                    name="lead_comment_url" 
                                    id="lead_comment_url" 
                                    :label="__('Interaction/Comment URL')" 
                                    placeholder="https://facebook.com/groups/.../posts/..." 
                                />
                            </div>

                            <x-ui::textarea 
                                wire:model="lead_content" 
                                name="lead_content" 
                                id="lead_content" 
                                :label="__('Interest/Chat Content (Nội dung quan tâm)')" 
                                placeholder="Describe what the customer asked or commented (e.g. IB báo giá, Tư vấn giúp em)..." 
                                rows="3"
                            />

                            <!-- Custom submit button due to package bug -->
                            <button type="submit" class="w-full inline-flex items-center justify-center font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-emerald-600 dark:hover:bg-emerald-700 px-4 py-2.5 text-sm cursor-pointer shadow-sm">
                                {{ __('Log Lead & Sync to Zenify') }}
                            </button>
                        </form>
                    </x-ui::card>
                </div>
            @else

                <!-- No Account Selected View -->
                <x-ui::card :bordered="true">
                    <div class="text-center py-16 space-y-4">
                        <div class="h-16 w-16 mx-auto bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center text-gray-400">
                            <x-ui::icon name="users" size="xl" />
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('No Account Selected') }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ __('Choose a seeder account from the sidebar list to inspect status constraints and submit new logs.') }}
                            </p>
                        </div>
                    </div>
                </x-ui::card>
            @endif
        </div>
    </div>
</div>
