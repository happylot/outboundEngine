<div class="flex h-full w-full flex-1 flex-col gap-6 p-4">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('AI Seeding Scripts Generator') }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ __('Define actual marketing scenarios, insert URLs, and let AI write psychological seeding comments (Greed, Anger, Fomo, Brand Recall) to redirect opinions.') }}
        </p>
    </div>

    <!-- Main Grid -->
    <div class="grid gap-6 md:grid-cols-3 flex-1 items-start">
        
        <!-- Left Column: Scenarios List & Add Form (1/3) -->
        <div class="space-y-6 md:col-span-1">
            <!-- Scenarios List -->
            <x-ui::card :title="__('Seeding Scenarios')" :subtitle="__('List of active opinion-redirection tasks.')" :bordered="true">
                @if (session()->has('scenario_success'))
                    <div class="mb-4">
                        <x-ui::alert type="success" :message="session('scenario_success')" />
                    </div>
                @endif

                <div class="space-y-3 max-h-[350px] overflow-y-auto pr-1">
                    @forelse($scenarios as $scen)
                        <div class="group relative rounded-lg border p-3 transition-all flex items-center justify-between {{ $selectedScenarioId === $scen->id ? 'border-indigo-600 bg-indigo-50/50 dark:border-indigo-500 dark:bg-indigo-950/20 ring-1 ring-indigo-500' : 'border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600 bg-white dark:bg-gray-800' }}">
                            <button 
                                wire:click="selectScenario({{ $scen->id }})"
                                class="w-full text-left pr-8"
                            >
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $scen->title }}</h4>
                                <p class="text-xs text-indigo-600 dark:text-indigo-400 font-medium capitalize mt-0.5">{{ $scen->brand_name }}</p>
                                <p class="text-xs text-gray-500 truncate max-w-[200px] mt-1">{{ $scen->context }}</p>
                            </button>

                            <!-- Delete button -->
                            <button 
                                onclick="confirm('Are you sure you want to delete this scenario?') || event.stopImmediatePropagation()"
                                wire:click="deleteScenario({{ $scen->id }})"
                                class="absolute right-3 top-3 text-gray-400 hover:text-red-600 transition-colors"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    @empty
                        <p class="text-center text-sm text-gray-500 dark:text-gray-400 py-6">
                            {{ __('No scenarios created yet.') }}
                        </p>
                    @endforelse
                </div>
            </x-ui::card>

            <!-- Add Scenario Form -->
            <x-ui::card :title="__('Create Seeding Scenario')" :bordered="true">
                <form wire:submit.prevent="createScenario" class="space-y-4">
                    <x-ui::input 
                        wire:model="title" 
                        name="title" 
                        id="title" 
                        :label="__('Scenario Title')" 
                        :required="true"
                        placeholder="e.g. Chống chê đắt sản phẩm A" 
                    />
                    
                    <x-ui::input 
                        wire:model="brand_name" 
                        name="brand_name" 
                        id="brand_name" 
                        :label="__('Brand Name')" 
                        :required="true"
                        placeholder="e.g. UGCMetrics Shop" 
                    />

                    <x-ui::input 
                        wire:model="target_link" 
                        name="target_link" 
                        id="target_link" 
                        :label="__('Target Content URL (Link cần seed)')" 
                        :required="true"
                        placeholder="https://facebook.com/... or https://tiktok.com/..." 
                    />

                    <x-ui::textarea 
                        wire:model="context" 
                        name="context" 
                        id="context" 
                        :label="__('Real-world Situation (Tình huống thực tế)')" 
                        :required="true"
                        placeholder="e.g. Khách hàng chê phí giao hàng quá cao, hoặc sản phẩm A bị chê đắt so với đối thủ B..." 
                    />

                    <button type="submit" class="w-full inline-flex items-center justify-center font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-600 dark:hover:bg-blue-700 px-4 py-2.5 text-sm cursor-pointer shadow-sm">
                        {{ __('Save & Generate AI Comments') }}
                    </button>
                </form>
            </x-ui::card>
        </div>

        <!-- Right Column: Script Details & AI Output (2/3) -->
        <div class="md:col-span-2 space-y-6">
            @if (session()->has('scripts_success'))
                <x-ui::alert type="success" :message="session('scripts_success')" />
            @endif

            @if($selectedScenario)
                <!-- Scenario Meta Information -->
                <x-ui::card :title="$selectedScenario->title" :bordered="true">
                    <div class="space-y-3 text-sm">
                        <div class="grid grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-800/40 p-4 rounded-lg border border-gray-100 dark:border-gray-800/50">
                            <div>
                                <span class="text-xs text-gray-400 block mb-0.5">{{ __('Brand Name') }}</span>
                                <span class="font-semibold text-gray-900 dark:text-white capitalize">{{ $selectedScenario->brand_name }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-400 block mb-0.5">{{ __('Target Link') }}</span>
                                <a href="{{ $selectedScenario->target_link }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium break-all block">
                                    {{ $selectedScenario->target_link }}
                                </a>
                            </div>
                        </div>
                        <div>
                            <span class="text-xs text-gray-400 block mb-0.5">{{ __('Real Situation Context') }}</span>
                            <p class="text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-3 rounded border border-gray-200/60 dark:border-gray-700">
                                {{ $selectedScenario->context }}
                            </p>
                        </div>

                        <!-- Regenerate Button -->
                        <div class="pt-2 flex justify-end">
                            <x-ui::button 
                                type="button" 
                                variant="secondary" 
                                wire:click="generateScriptsForScenario({{ $selectedScenario->id }})"
                                icon="arrow-path"
                                :disabled="$isGenerating"
                            >
                                {{ $isGenerating ? __('Generating scripts...') : __('Regenerate AI Scripts') }}
                            </x-ui::button>
                        </div>
                    </div>
                </x-ui::card>

                <!-- AI Scripts Output by Angles -->
                @if($selectedScenario->templates->isNotEmpty())
                    <div class="space-y-6">
                        <h3 class="text-md font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <x-ui::icon name="sparkles" class="text-indigo-600 dark:text-indigo-400" />
                            {{ __('AI Psychology Seeding Scripts') }}
                        </h3>

                        <div class="grid gap-6 sm:grid-cols-2" x-data="{
                            copyToClipboard(text, id) {
                                navigator.clipboard.writeText(text);
                                const el = document.getElementById('copied-badge-' + id);
                                if (el) {
                                    el.style.opacity = '1';
                                    setTimeout(() => el.style.opacity = '0', 2000);
                                }
                            }
                        }">
                            <!-- Angle: THAM -->
                            @php $thamTemplates = $selectedScenario->templates->where('angle', 'tham'); @endphp
                            <x-ui::card class="border-emerald-200 bg-emerald-50/20 dark:border-emerald-900/50 dark:bg-emerald-950/10">
                                <x-slot name="header">
                                    <div class="flex items-center gap-2 border-b border-emerald-100 dark:border-emerald-900/40 pb-2">
                                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                        <h4 class="text-sm font-extrabold text-emerald-800 dark:text-emerald-400 uppercase tracking-wide">
                                            {{ __('THAM (Lòng tham - Lợi ích)') }}
                                        </h4>
                                    </div>
                                </x-slot>
                                <div class="space-y-3 pt-2">
                                    @foreach($thamTemplates as $temp)
                                        <div class="relative bg-white dark:bg-gray-800/80 p-3 rounded border border-emerald-100 dark:border-emerald-900/30">
                                            <p class="text-xs text-gray-800 dark:text-gray-200 leading-relaxed pr-10">{{ $temp->content }}</p>
                                            
                                            <!-- Copy Button -->
                                            <div class="absolute right-2 top-2 flex items-center gap-1.5">
                                                <span id="copied-badge-{{ $temp->id }}" class="opacity-0 transition-opacity duration-200 text-[10px] font-bold text-emerald-600 bg-emerald-100 dark:bg-emerald-950/80 px-1.5 py-0.5 rounded">Copied!</span>
                                                <button @click="copyToClipboard('{{ addslashes($temp->content) }}', {{ $temp->id }})" class="text-gray-400 hover:text-emerald-600 transition-colors p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700">
                                                    <x-ui::icon name="document-duplicate" size="sm" />
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </x-ui::card>

                            <!-- Angle: SÂN -->
                            @php $sanTemplates = $selectedScenario->templates->where('angle', 'san'); @endphp
                            <x-ui::card class="border-rose-200 bg-rose-50/20 dark:border-rose-900/50 dark:bg-rose-950/10">
                                <x-slot name="header">
                                    <div class="flex items-center gap-2 border-b border-rose-100 dark:border-rose-900/40 pb-2">
                                        <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                                        <h4 class="text-sm font-extrabold text-rose-800 dark:text-rose-400 uppercase tracking-wide">
                                            {{ __('SÂN (Sự phản biện/Phẫn nộ)') }}
                                        </h4>
                                    </div>
                                </x-slot>
                                <div class="space-y-3 pt-2">
                                    @foreach($sanTemplates as $temp)
                                        <div class="relative bg-white dark:bg-gray-800/80 p-3 rounded border border-rose-100 dark:border-rose-900/30">
                                            <p class="text-xs text-gray-800 dark:text-gray-200 leading-relaxed pr-10">{{ $temp->content }}</p>
                                            
                                            <!-- Copy Button -->
                                            <div class="absolute right-2 top-2 flex items-center gap-1.5">
                                                <span id="copied-badge-{{ $temp->id }}" class="opacity-0 transition-opacity duration-200 text-[10px] font-bold text-rose-600 bg-rose-100 dark:bg-rose-950/80 px-1.5 py-0.5 rounded">Copied!</span>
                                                <button @click="copyToClipboard('{{ addslashes($temp->content) }}', {{ $temp->id }})" class="text-gray-400 hover:text-rose-600 transition-colors p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700">
                                                    <x-ui::icon name="document-duplicate" size="sm" />
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </x-ui::card>

                            <!-- Angle: SI -->
                            @php $siTemplates = $selectedScenario->templates->where('angle', 'si'); @endphp
                            <x-ui::card class="border-amber-200 bg-amber-50/20 dark:border-amber-900/50 dark:bg-amber-950/10">
                                <x-slot name="header">
                                    <div class="flex items-center gap-2 border-b border-amber-100 dark:border-amber-900/40 pb-2">
                                        <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                                        <h4 class="text-sm font-extrabold text-amber-800 dark:text-amber-400 uppercase tracking-wide">
                                            {{ __('SI (Sự tò mò/FOMO/Hùa theo)') }}
                                        </h4>
                                    </div>
                                </x-slot>
                                <div class="space-y-3 pt-2">
                                    @foreach($siTemplates as $temp)
                                        <div class="relative bg-white dark:bg-gray-800/80 p-3 rounded border border-amber-100 dark:border-amber-900/30">
                                            <p class="text-xs text-gray-800 dark:text-gray-200 leading-relaxed pr-10">{{ $temp->content }}</p>
                                            
                                            <!-- Copy Button -->
                                            <div class="absolute right-2 top-2 flex items-center gap-1.5">
                                                <span id="copied-badge-{{ $temp->id }}" class="opacity-0 transition-opacity duration-200 text-[10px] font-bold text-amber-600 bg-amber-100 dark:bg-amber-950/80 px-1.5 py-0.5 rounded">Copied!</span>
                                                <button @click="copyToClipboard('{{ addslashes($temp->content) }}', {{ $temp->id }})" class="text-gray-400 hover:text-amber-600 transition-colors p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700">
                                                    <x-ui::icon name="document-duplicate" size="sm" />
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </x-ui::card>

                            <!-- Angle: BRAND RECALL -->
                            @php $recallTemplates = $selectedScenario->templates->where('angle', 'brand_recall'); @endphp
                            <x-ui::card class="border-indigo-200 bg-indigo-50/20 dark:border-indigo-900/50 dark:bg-indigo-950/10">
                                <x-slot name="header">
                                    <div class="flex items-center gap-2 border-b border-indigo-100 dark:border-indigo-900/40 pb-2">
                                        <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                                        <h4 class="text-sm font-extrabold text-indigo-800 dark:text-indigo-400 uppercase tracking-wide">
                                            {{ __('BRAND RECALL (Nhắc nhớ thương hiệu)') }}
                                        </h4>
                                    </div>
                                </x-slot>
                                <div class="space-y-3 pt-2">
                                    @foreach($recallTemplates as $temp)
                                        <div class="relative bg-white dark:bg-gray-800/80 p-3 rounded border border-indigo-100 dark:border-indigo-900/30">
                                            <p class="text-xs text-gray-800 dark:text-gray-200 leading-relaxed pr-10">{{ $temp->content }}</p>
                                            
                                            <!-- Copy Button -->
                                            <div class="absolute right-2 top-2 flex items-center gap-1.5">
                                                <span id="copied-badge-{{ $temp->id }}" class="opacity-0 transition-opacity duration-200 text-[10px] font-bold text-indigo-600 bg-indigo-100 dark:bg-indigo-950/80 px-1.5 py-0.5 rounded">Copied!</span>
                                                <button @click="copyToClipboard('{{ addslashes($temp->content) }}', {{ $temp->id }})" class="text-gray-400 hover:text-indigo-600 transition-colors p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700">
                                                    <x-ui::icon name="document-duplicate" size="sm" />
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </x-ui::card>
                        </div>

                        <!-- Direct shortcut to Workspace -->
                        <div class="pt-4 flex justify-center">
                            <x-ui::button 
                                variant="primary" 
                                :href="route('seeding.workspace')" 
                                icon="clipboard-document-list"
                            >
                                {{ __('Go to Tester Workspace to Log Interaction') }}
                            </x-ui::button>
                        </div>
                    </div>
                @else
                    <x-ui::card :bordered="true">
                        <div class="text-center py-16 space-y-4">
                            <div class="h-16 w-16 mx-auto bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center text-gray-400">
                                <x-ui::icon name="sparkles" size="xl" />
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('No AI Scripts Found') }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    {{ __('Click the "Regenerate AI Scripts" button above to generate comments.') }}
                                </p>
                            </div>
                        </div>
                    </x-ui::card>
                @endif
            @else
                <!-- No Scenario Selected View -->
                <x-ui::card :bordered="true">
                    <div class="text-center py-20 space-y-4">
                        <div class="h-16 w-16 mx-auto bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center text-gray-400">
                            <x-ui::icon name="document-duplicate" size="xl" />
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('No Scenario Selected') }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ __('Choose a seeding scenario from the sidebar list or create a new one to generate AI-tailored comments.') }}
                            </p>
                        </div>
                    </div>
                </x-ui::card>
            @endif
        </div>
    </div>
</div>
