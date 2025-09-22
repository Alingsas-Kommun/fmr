<div class="max-w-lg mx-auto relative">
    <form class="flex gap-x-4">
        <label for="search" class="sr-only">{{ __('Search', 'fmr') }}</label>
        <div class="flex-auto relative">
            <input 
                wire:model.live="query"
                type="text" 
                id="search"
                class="block w-full rounded-lg border-0 bg-white px-4 py-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6 focus:outline-hidden" 
                placeholder="{{ __('Search elected officials...', 'fmr') }}"
                autocomplete="off"
            >

            @if ($query && $results->isNotEmpty())
                <div class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-80 overflow-y-auto">
                    <div class="py-2 px-2">
                        @foreach ($results as $result)
                            <a href="{{ $result['url'] }}" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 group">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        @if($result['thumbnail'])
                                            <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
                                                {!! $result['thumbnail'] !!}
                                            </div>
                                        @else
                                            <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-medium text-gray-900 group-hover:text-emerald-600 transition-colors duration-200">
                                            {{ $result['title'] }}
                                        </h3>
                                    </div>
                                    
                                    <div class="flex-shrink-0">
                                        <svg class="w-4 h-4 text-gray-400 group-hover:text-emerald-600 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @elseif ($query && $results->isEmpty())
                <div class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                    <div class="px-4 py-6">
                        <x-alert type="warning">
                            <p class="text-sm text-gray-800">{{ sprintf(__('No elected officials found for "%s"', 'fmr'), $query) }}</p>
                            <p class="text-xs text-gray-700 mt-1">{{ __('Try a different name', 'fmr') }}</p>
                        </x-alert>
                    </div>
                </div>
            @endif
        </div>

        <button type="button" class="flex-none rounded-lg bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600">
            {{ __('Search', 'fmr') }}
        </button>
    </form>
</div>