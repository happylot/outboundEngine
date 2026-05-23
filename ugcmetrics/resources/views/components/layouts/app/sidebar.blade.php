<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-r border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                    
                    <flux:navlist.group expandable heading="Data & Metrics">
                        <flux:navlist.item icon="users" :href="route('influencer.index')" :current="request()->routeIs('influencer.*')" >{{ __('Influencer') }}</flux:navlist.item>
                        <flux:navlist.item icon="camera" :href="route('instagram.index')" :current="request()->routeIs('instagram.index')">{{ __('Instagram') }}</flux:navlist.item>
                        <flux:navlist.item icon="chat-bubble-left" :href="route('twitter.index')" :current="request()->routeIs('twitter.index')">{{ __('Twitter') }}</flux:navlist.item>
                        <flux:navlist.item icon="video-camera" :href="route('twitch.index')" :current="request()->routeIs('twitch.*')">{{ __('Twitch') }}</flux:navlist.item>
                        <flux:navlist.item icon="globe-alt" :href="route('reddit.index')" :current="request()->routeIs('reddit.*')" >{{ __('Reddit') }}</flux:navlist.item>
                    </flux:navlist.group>
                    
                    <flux:navlist.group expandable heading="Seeding Stage">
                        <flux:navlist.item icon="chart-bar" :href="route('seeding.dashboard')" :current="request()->routeIs('seeding.dashboard')" wire:navigate>{{ __('Seeding Dashboard') }}</flux:navlist.item>
                        <flux:navlist.item icon="clipboard-document-list" :href="route('seeding.workspace')" :current="request()->routeIs('seeding.workspace')" wire:navigate>{{ __('Tester Workspace') }}</flux:navlist.item>
                        <flux:navlist.item icon="document-duplicate" :href="route('seeding.scenarios')" :current="request()->routeIs('seeding.scenarios')" wire:navigate>{{ __('Seeding Scripts') }}</flux:navlist.item>
                    </flux:navlist.group>

                    <flux:navlist.group expandable heading="Outbound Sales">
                        <flux:navlist.item icon="book-open" :href="route('outbound.sales-brain')" :current="request()->routeIs('outbound.sales-brain')" wire:navigate>{{ __('Sales Brain KB') }}</flux:navlist.item>
                        <flux:navlist.item icon="users" :href="route('outbound.leads')" :current="request()->routeIs('outbound.leads')" wire:navigate>{{ __('Outbound Leads') }}</flux:navlist.item>
                        <flux:navlist.item icon="magnifying-glass-circle" :href="route('outbound.research')" :current="request()->routeIs('outbound.research')" wire:navigate>{{ __('Research Workspace') }}</flux:navlist.item>
                        <flux:navlist.item icon="arrow-path-rounded-square" :href="route('outbound.reactivation')" :current="request()->routeIs('outbound.reactivation')" wire:navigate>{{ __('Reactivation Engine') }}</flux:navlist.item>
                    </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

            <flux:navlist variant="outline">
                <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                {{ __('Repository') }}
                </flux:navlist.item>

                <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits" target="_blank">
                {{ __('Documentation') }}
                </flux:navlist.item>
            </flux:navlist>

            <!-- Desktop User Menu -->
            <flux:dropdown position="bottom" align="start">
                @auth
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevrons-up-down"
                />
        
                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>
                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>
        
                    <flux:menu.separator />
        
                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>
        
                    <flux:menu.separator />
        
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            @else
                <flux:profile
                    name="Guest"
                    initials="G"
                    icon-trailing="chevrons-up-down"
                />
        
                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('login')" icon="user" wire:navigate>{{ __('Login') }}</flux:menu.item>
                        <flux:menu.item :href="route('register')" icon="user-plus" wire:navigate>{{ __('Register') }}</flux:menu.item>
                    </flux:menu.radio.group>
                </flux:menu>
            @endauth
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                @auth
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />
        
                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>
                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>
        
                    <flux:menu.separator />
        
                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>
        
                    <flux:menu.separator />
        
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            @else
                <flux:profile
                    initials="G"
                    icon-trailing="chevron-down"
                />
        
                <flux:menu>
                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('login')" icon="user" wire:navigate>{{ __('Login') }}</flux:menu.item>
                        <flux:menu.item :href="route('register')" icon="user-plus" wire:navigate>{{ __('Register') }}</flux:menu.item>
                    </flux:menu.radio.group>
                </flux:menu>
            @endauth
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
