<div class="md:bg-primary-50 rounded-lg overflow-hidden md:p-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center">
            {{ $board->post_title }}
            
            @if($board->shortening)
                <span class="text-2xl font-normal text-gray-600 ml-2">
                    ({{ $board->shortening }})
                </span>
            @endif
        </h1>

        @if($board->category)
            <div class="flex items-center space-x-2">
                <x-heroicon-o-tag class="h-5 w-5 text-primary-600 flex-shrink-0" />
                
                <div class="text-gray-700">
                    {{ $board->category->name }}
                </div>
            </div>
        @endif
    </div>

    @if($board->website || $board->email || $board->phone)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-gray-200 py-5 mt-5">
            @if($board->website)
                <div class="flex items-start space-x-3">
                    <x-heroicon-o-globe-alt class="h-5 w-5 text-primary-600 flex-shrink-0" />
                    
                    <div class="text-gray-700">
                        <div class="font-bold">{!! __('Website', 'fmr') !!}</div>
                        
                        <x-link href="{{ $board->website }}" target="_blank">
                            {{ $board->website }}
                        </x-link>
                    </div>
                </div>
            @endif

            @if($board->email)
                <div class="flex items-start space-x-3">
                    <x-heroicon-o-envelope class="h-5 w-5 text-primary-600 flex-shrink-0" />
                    
                    <div class="text-gray-700">
                        <div class="font-bold">{!! __('Email', 'fmr') !!}</div>
                        
                        <x-link href="mailto:{{ $board->email }}">
                            {{ $board->email }}
                        </x-link>
                    </div>
                </div>
            @endif

            @if($board->phone)
                <div class="flex items-start space-x-3">
                    <x-heroicon-o-phone class="h-5 w-5 text-primary-600 flex-shrink-0" />
                    
                    <div class="text-gray-700">
                        <div class="font-bold">{!! __('Phone', 'fmr') !!}</div>
                        
                        <x-link href="tel:{{ $board->phone }}">
                            {{ $board->phone }}
                        </x-link>
                    </div>
                </div>
            @endif
        </div>
    @endif

    @if($board->address || $board->zip || $board->city || $board->visitingAddress)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-gray-200 pt-5">
            @if($board->address || $board->zip || $board->city)
                <div class="flex items-start space-x-3">
                    <x-heroicon-o-map-pin class="h-5 w-5 text-primary-600 mt-0.5 flex-shrink-0" />
                    
                    <div class="text-gray-700">
                        <div class="font-bold">{!! __('Address', 'fmr') !!}</div>
                        
                        @if($board->address)
                            <div>{{ $board->address }}</div>
                        @endif

                        @if($board->zip || $board->city)
                            <div>
                                @if($board->zip){{ $board->zip }}@endif
                                @if($board->zip && $board->city), @endif
                                @if($board->city){{ $board->city }}@endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if($board->visitingAddress)
                <div class="flex items-start space-x-3">
                    <x-heroicon-o-building-office class="h-5 w-5 text-primary-600 mt-0.5 flex-shrink-0" />
                    
                    <div class="text-gray-700">
                        <div class="font-bold">{!! __('Visiting Address', 'fmr') !!}</div>
                        <div>{{ $board->visitingAddress }}</div>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>

@if($decisionAuthorities->isNotEmpty())
    <div class="mt-6">
        <h2 class="text-2xl font-semibold mb-4">{!! __('Decision Authorities', 'fmr') !!}</h2>
        <div class="bg-white dark:bg-gray-100 rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100 dark:bg-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                            {!! __('Title', 'fmr') !!}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                            {!! __('Type', 'fmr') !!}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                            {!! __('Period', 'fmr') !!}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider sr-only">
                            {!! __('Actions', 'fmr') !!}
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-gray-50 dark:bg-gray-100 divide-y divide-gray-200">
                    @foreach($decisionAuthorities as $authority)
                        <tr>
                            <td class="px-6 py-4">
                                <x-link href="{{ route('decision-authorities.show', $authority) }}">
                                    {{ $authority->title }}
                                </x-link>
                            </td>
                            
                            <td class="px-6 py-4">
                                @if($authority->typeTerm)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-50 text-gray-800">
                                        {{ $authority->typeTerm->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400 italic">{{ __('No type assigned', 'fmr') }}</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    {{ $authority->start_date->format('j M Y') }} - {{ $authority->end_date->format('j M Y') }}
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <x-link href="{{ route('decision-authorities.show', $authority) }}">
                                    {!! __('View', 'fmr') !!}
                                </x-link>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
