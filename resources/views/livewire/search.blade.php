<div class="max-w-lg mx-auto relative">
    <form wire:submit="search" class="flex gap-x-4">
        <label for="search" class="sr-only">{{ __('Search', 'fmr') }}</label>
        <div class="flex-auto relative">
            <input 
                wire:model.live="query"
                type="text" 
                id="search"
                class="block w-full rounded-lg border-0 bg-white px-4 py-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6 focus:outline-hidden" 
                placeholder="{{ __('Search elected officials...', 'fmr') }}"
                autocomplete="off"
            >

            @if ($query && $results->isNotEmpty())
                <div class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg z-50 max-h-80 overflow-y-auto">
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
                                                <x-heroicon-o-user class="w-7 h-7 text-emerald-600" />
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-md font-medium text-gray-900 group-hover:text-emerald-600 transition-colors duration-200">
                                            {{ $result->title }}
                                        </h3>

                                        @if($result->party)
                                            <div class="flex items-center space-x-1 text-gray-800 hover:text-gray-900">
                                                @if($result->party->thumbnail)
                                                    <div class="w-4 h-4 flex-shrink-0">
                                                        {!! $result->party->thumbnail !!}
                                                    </div>
                                                @else
                                                    <x-heroicon-o-user-group class="h-4 w-4 text-emerald-600 flex-shrink-0" />
                                                @endif

                                                <span class="text-sm">{!! $result->party->title !!}</span>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex-shrink-0">
                                        <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-400 group-hover:text-emerald-600 transition-colors duration-200" />
                                    </div>
                                </div>
                            </a>
                        @endforeach
                        
                    </div>
                </div>
            @elseif ($query && $results->isEmpty())
                <div class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg z-50">
                    <div class="px-4 py-6">
                        <x-alert type="warning">
                            <p class="text-sm text-gray-800">{{ sprintf(__('No elected officials found for "%s"', 'fmr'), $query) }}</p>
                            <p class="text-xs text-gray-700 mt-1">{{ __('Try a different name', 'fmr') }}</p>
                        </x-alert>
                    </div>
                </div>
            @endif
        </div>

        <button type="submit" :disabled="!$wire.query" class="flex-none rounded-lg cursor-pointer bg-emerald-600 disabled:opacity-50 px-6 py-3 text-sm font-semibold text-white hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600">
            {{ __('Search', 'fmr') }}
        </button>
    </form>

    <div class="mt-4 text-center">
        <a 
            href="{{ route('search.show') }}" 
            class="inline-flex items-center text-sm font-medium text-emerald-800 hover:text-emerald-900 transition-colors duration-200"
        >
            <x-heroicon-o-adjustments-horizontal class="w-4 h-4 mr-1" />
            <span>{{ __('Search with more advanced search criteria', 'fmr') }}</span>
        </a>
    </div>
</div>