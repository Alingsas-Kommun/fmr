<div class="max-w-lg mx-auto relative" x-data="{ showResults: true }" x-on:click.outside="showResults = false" x-on:keydown.escape.window="showResults = false">
    <form wire:submit="search" class="flex gap-x-4">
        <label for="search" class="sr-only">{{ __('Search', 'fmr') }}</label>
        <div class="flex-auto relative">
            <input 
                wire:model.live="query"
                type="text" 
                id="search"
                class="block w-full rounded-lg border-0 bg-white px-4 py-3 text-gray-900 placeholder:text-gray-600 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:text-sm sm:leading-6 focus:outline-hidden" 
                placeholder="{{ __('Search elected officials...', 'fmr') }}"
                autocomplete="off"
                x-on:focus="showResults = true"
                x-on:input="showResults = true"
            >

            <div x-show="showResults && $wire.query" 
                x-transition:enter="transition ease-out duration-100" 
                x-transition:enter-start="opacity-0 scale-95" 
                x-transition:enter-end="opacity-100 scale-100" 
                x-transition:leave="transition ease-in duration-75" 
                x-transition:leave-start="opacity-100 scale-100" 
                x-transition:leave-end="opacity-0 scale-95" 
                class="absolute top-full left-0 right-0 mt-1 z-50"
            >
                
                <div wire:loading class="overflow-y-auto bg-white border border-gray-200 rounded-lg">
                    <div class="py-2 px-2">
                        @for ($i = 0; $i < 2; $i++)
                            <div class="block w-100 px-4 py-3 border-b border-gray-100 last-of-type:border-b-0">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-gray-200 rounded-full animate-pulse"></div>
                                    </div>
                                    
                                    <div class="flex-1 min-w-0 space-y-2">
                                        <div class="h-4 bg-gray-200 rounded animate-pulse w-3/4"></div>
                                        <div class="h-3 bg-gray-200 rounded animate-pulse w-1/2"></div>
                                    </div>
                                    
                                    <div class="flex-shrink-0">
                                        <div class="w-4 h-4 bg-gray-200 rounded animate-pulse"></div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

                <div wire:loading.remove>
                    @if ($query && $results && $results->isNotEmpty())
                        <div class="max-h-80 overflow-y-auto bg-white border border-gray-200 rounded-lg">
                            <div class="py-2 px-2">
                                @foreach ($results as $result)
                                    <a href="{{ $result->url }}" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last-of-type:border-b-0 group">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                @if($result->thumbnail)
                                                    <div class="w-12 h-12 rounded-full overflow-hidden flex-shrink-0">
                                                        {!! $result->thumbnail !!}
                                                    </div>
                                                @else
                                                    <div class="w-12 h-12 bg-gray-50 border border-gray-200 rounded-full flex items-center justify-center">
                                                        <x-heroicon-o-user class="w-7 h-7 text-primary-600" />
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="flex-1 min-w-0">
                                                <h3 class="text-md font-medium text-gray-900 group-hover:text-primary-700 transition-colors duration-200">
                                                    {{ $result->title }}
                                                </h3>

                                                @if($result->party)
                                                    <div class="flex items-center space-x-1 text-gray-800 hover:text-gray-900">
                                                        @if($result->party->thumbnail)
                                                            <div class="w-4 h-4 flex-shrink-0">
                                                                {!! $result->party->thumbnail !!}
                                                            </div>
                                                        @else
                                                            <x-heroicon-o-user-group class="h-4 w-4 text-primary-600 flex-shrink-0" />
                                                        @endif

                                                        <span class="text-sm">{!! $result->party->title !!}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="flex-shrink-0">
                                                <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-400 group-hover:text-primary-700 transition-colors duration-200" />
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @elseif ($query && $results && $results->isEmpty())
                        <div class="px-4 py-6 bg-white border border-gray-200 rounded-lg">
                            <x-alert type="warning">
                                <p class="text-sm text-gray-800">{{ sprintf(__('No elected officials found for "%s"', 'fmr'), $query) }}</p>
                                <p class="text-xs text-gray-700 mt-1">{{ __('Try a different name', 'fmr') }}</p>
                            </x-alert>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <button type="submit" :disabled="!$wire.query" class="flex-none rounded-lg cursor-pointer bg-tertiary-500 hover:bg-tertiary-600 disabled:opacity-50 px-6 py-3 text-sm font-semibold text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-tertiary-500">
            {{ __('Search', 'fmr') }}
        </button>
    </form>

    @if($setting('show_advanced_search', true))
        <div class="mt-4 text-center">
            <x-link :href="route('search.show')">
                <x-heroicon-o-adjustments-horizontal class="w-4 h-4 mr-1" />
                <span>{{ __('Search with more advanced search criteria', 'fmr') }}</span>
            </x-link>
        </div>
    @endif
</div>