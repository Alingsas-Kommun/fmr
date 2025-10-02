<div 
    x-data="{ 
        scrolled: false,
        init() {
            this.updateScrollState();
            window.addEventListener('scroll', () => this.updateScrollState());
        },
        updateScrollState() {
            this.scrolled = window.scrollY > 5;
        }
    }"
    x-cloak
    class="relative"
>
    <div x-show="scrolled" class="h-18 transition-all duration-300" style="display: none;"></div>

    <header 
        class="relative transition-all duration-300"
        :class="{
            'fixed top-0 left-0 right-0 z-50 px-4 pt-2': scrolled,
            'relative': !scrolled
        }"
    >
        <div 
            class="max-w-5xl px-4 mx-auto bg-white dark:bg-gray-50 border-white dark:border-gray-50 transition-all duration-300"
            :class="{
                'max-w-5xl mx-auto bg-white/80 dark:bg-gray-100/80 backdrop-blur-xl backdrop-saturate-200 rounded-xl shadow-xl shadow-gray-200/20 dark:shadow-transparent border border-gray-300/30': scrolled,
                'max-w-5xl px-4 mx-auto bg-white dark:bg-gray-50 border-white dark:border-gray-50': !scrolled
            }"
        >
            <div 
                class="py-3 transition-all duration-300"
                :class="{
                    'py-3 px-6': scrolled,
                    'py-3': !scrolled
                }"
            >
                <div class="flex items-center justify-between">
                    <a href="{{ route('homepage') }}" class="flex-shrink-0">
                        @if($logotype)
                            {!! $logotype !!}
                        @else
                            <span class="text-2xl font-bold">{{ $siteName }}</span>
                        @endif
                    </a>

                    <a class="sr-only focus:not-sr-only" href="#main">
                        {{ __('Skip to content', 'fmr') }}
                    </a>

                    <div class="flex items-center space-x-6">
                        @user
                            <nav class="hidden md:flex items-center space-x-5">
                                <a href="{{ route('assignments.index') }}" class="text-gray-700 hover:text-gray-800 font-semibold">
                                    {!! __('Assignments', 'fmr') !!}
                                </a>

                                <a href="{{ route('decision-authorities.index') }}" class="text-gray-700 hover:text-gray-800 font-semibold">
                                    {!! __('Decision Authorities', 'fmr') !!}
                                </a>
                            </nav>

                            @set($current_user, wp_get_current_user())
                            @set($avatar_url, get_avatar_url($current_user->ID, ['size' => 100]))

                            <div class="hidden md:block">
                                <x-dropdown align="right" width="48" contentClasses="divide-y divide-gray-100 dark:divide-gray-300 bg-white dark:bg-gray-100">
                                    <x-slot name="trigger">
                                        <button class="flex items-center space-x-2 text-sm px-4 py-2 bg-gray-200/50 dark:bg-gray-300/50 border border-gray-300/30 rounded-md transition-colors duration-200">
                                            <img src="{{ $avatar_url }}" alt="{{ $current_user->display_name }}" class="w-7 h-7 rounded-full">
                                            
                                            <span class="font-semibold text-gray-700">
                                                {{ $current_user->display_name }}
                                            </span>
                                            
                                            <x-heroicon-s-chevron-down class="w-4 h-4 text-gray-700" />
                                        </button>
                                    </x-slot>

                                    <x-slot name="content">
                                        <div class="px-2 py-3">
                                            <x-dropdown-link href="{{ admin_url('profile.php') }}">
                                                <x-slot name="icon">
                                                    <x-heroicon-o-user-circle class="w-5 h-5 text-primary-500" />
                                                </x-slot>

                                                {{ __('Profile', 'fmr') }}
                                            </x-dropdown-link>

                                            <x-dropdown-link href="{!! admin_url('options-general.php?page=configuration') !!}">
                                                <x-slot name="icon">
                                                    <x-heroicon-o-cog-6-tooth class="w-5 h-5 text-primary-500" />
                                                </x-slot>

                                                {{ __('Settings', 'fmr') }}
                                            </x-dropdown-link>

                                            <x-dropdown-link href="{!! admin_url() !!}">
                                                <x-slot name="icon">
                                                    <x-heroicon-o-squares-2x2 class="w-5 h-5 text-primary-500" />
                                                </x-slot>

                                                {{ __('Dashboard', 'fmr') }}
                                            </x-dropdown-link>
                                        </div>

                                        <div class="px-2 py-3">
                                            <x-dropdown-link href="{!! wp_logout_url(home_url()) !!}" class="text-red-600 hover:bg-red-50">
                                                <x-slot name="icon">
                                                    <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5" />
                                                </x-slot>

                                                {{ __('Logout', 'fmr') }}
                                            </x-dropdown-link>
                                        </div>
                                    </x-slot>
                                </x-dropdown>
                            </div>
                        @enduser

                        <x-theme-switcher />
                    </div>
                </div>
            </div>
        </div>
    </header>
</div>