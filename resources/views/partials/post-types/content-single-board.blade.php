@use('App\Utilities\TableColumn')
<div class="bg-primary-50 rounded-lg overflow-hidden p-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center">
            {{ $board->name }}
            
            @if($board->meta->shortening)
                <span class="text-2xl font-normal text-gray-600 ml-2">
                    ({{ $board->meta->shortening }})
                </span>
            @endif
        </h1>

        @if($board->category)
            <div class="flex items-center space-x-2">
                <x-heroicon-o-tag class="h-5 w-5 text-primary-600 flex-shrink-0" />
                
                <div class="text-gray-700">
                    {{ $board->category }}
                </div>
            </div>
        @endif
    </div>

    @if($board->meta->website || $board->meta->email || $board->meta->phone)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-gray-200 py-5 mt-5">
            @if($board->meta->website)
                <div class="flex items-start space-x-3">
                    <x-heroicon-o-globe-alt class="h-5 w-5 text-primary-600 flex-shrink-0" />
                    
                    <div class="text-gray-700">
                        <div class="font-bold">{!! __('Website', 'fmr') !!}</div>
                        
                        <x-link href="{{ $board->meta->website }}" target="_blank">
                            {{ $board->meta->website }}
                        </x-link>
                    </div>
                </div>
            @endif

            @if($board->meta->email)
                <div class="flex items-start space-x-3">
                    <x-heroicon-o-envelope class="h-5 w-5 text-primary-600 flex-shrink-0" />
                    
                    <div class="text-gray-700">
                        <div class="font-bold">{!! __('Email', 'fmr') !!}</div>
                        
                        <x-link href="mailto:{{ $board->meta->email }}">
                            {{ $board->meta->email }}
                        </x-link>
                    </div>
                </div>
            @endif

            @if($board->meta->phone)
                <div class="flex items-start space-x-3">
                    <x-heroicon-o-phone class="h-5 w-5 text-primary-600 flex-shrink-0" />
                    
                    <div class="text-gray-700">
                        <div class="font-bold">{!! __('Phone', 'fmr') !!}</div>
                        
                        <x-link href="tel:{{ $board->meta->phone }}">
                            {{ $board->meta->phone }}
                        </x-link>
                    </div>
                </div>
            @endif
        </div>
    @endif

    @if($board->meta->address || $board->meta->zip || $board->meta->city || $board->meta->visitingAddress)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-gray-200 pt-5">
            @if($board->meta->address || $board->meta->zip || $board->meta->city)
                <div class="flex items-start space-x-3">
                    <x-heroicon-o-map-pin class="h-5 w-5 text-primary-600 mt-0.5 flex-shrink-0" />
                    
                    <div class="text-gray-700">
                        <div class="font-bold">{!! __('Address', 'fmr') !!}</div>
                        
                        @if($board->meta->address)
                            <div>{{ $board->meta->address }}</div>
                        @endif

                        @if($board->meta->zip || $board->meta->city)
                            <div>
                                @if($board->meta->zip){{ $board->meta->zip }}@endif
                                @if($board->meta->zip && $board->meta->city), @endif
                                @if($board->meta->city){{ $board->meta->city }}@endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if($board->meta->visitingAddress)
                <div class="flex items-start space-x-3">
                    <x-heroicon-o-building-office class="h-5 w-5 text-primary-600 mt-0.5 flex-shrink-0" />
                    
                    <div class="text-gray-700">
                        <div class="font-bold">{!! __('Visiting Address', 'fmr') !!}</div>
                        <div>{{ $board->meta->visitingAddress }}</div>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>

@if(!empty($decisionAuthorities))
    <div class="mt-6">
        <h2 class="text-xl font-semibold mb-4">{!! __('Decision Authorities', 'fmr') !!}</h2>
        
        <div class="bg-white dark:bg-gray-100 rounded-lg border border-gray-200 overflow-hidden">
            @set($columns, [
                TableColumn::link('title.text', __('Title', 'fmr'), 'title.url', 'truncate max-w-60'),
                TableColumn::text('type', __('Type', 'fmr')),
                TableColumn::text('period', __('Period', 'fmr')),
                TableColumn::arrowLink('view.text', '', 'view.url')
            ])

            <x-sortable-table :data="$decisionAuthorities" :columns="$columns" :empty-message="__('No decision authorities found.', 'fmr')" class="w-full" />
        </div>
    </div>
@endif
