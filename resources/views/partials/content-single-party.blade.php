<div class="bg-white dark:bg-gray-100 rounded-lg shadow overflow-hidden">
    <div class="p-8">
        <div class="flex items-center space-x-6">
            @if($thumbnail)
                <div class="flex-shrink-0">
                    <div class="w-24 h-24 flex items-center justify-center">
                        {!! $thumbnail !!}
                    </div>
                </div>
            @endif
            
            <div class="flex-1 space-y-1">
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ $party->post_title }}

                    @if($party->shortening)
                        <span class="text-2xl font-normal text-gray-600">
                            ({{ $party->shortening }})
                        </span>
                    @endif
                </h1>
                
                @if($party->description)
                    <div class="prose max-w-none text-gray-700">
                        {!! wp_kses_post($party->description) !!}
                    </div>
                @endif  
            </div>
        </div>

        @set($hasContactInfo, $party->address || $party->zip || $party->city || $party->email || $party->phone || $party->website || $party->groupLeader)

        @if($hasContactInfo)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-gray-200 mt-8 pt-6">
                @if($party->address || $party->zip || $party->city)
                    <div class="flex items-start space-x-3">
                        <x-heroicon-o-map-pin class="h-5 w-5 text-emerald-600 mt-0.5 flex-shrink-0" />
                        
                        <div class="text-gray-700">
                            @if($party->address)
                                <div>{{ $party->address }}</div>
                            @endif

                            @if($party->zip || $party->city)
                                <div>
                                    @if($party->zip){{ $party->zip }}@endif
                                    @if($party->zip && $party->city), @endif
                                    @if($party->city){{ $party->city }}@endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if($party->email)
                    <div class="flex items-center space-x-3">
                        <x-heroicon-o-envelope class="h-5 w-5 text-emerald-600 flex-shrink-0" />
                        
                        <a href="mailto:{{ $party->email }}" class="text-emerald-700 hover:text-emerald-800">
                            {{ $party->email }}
                        </a>
                    </div>
                @endif

                @if($party->phone)
                    <div class="flex items-center space-x-3">
                        <x-heroicon-o-phone class="h-5 w-5 text-emerald-600 flex-shrink-0" />
                        
                        <a href="tel:{{ $party->phone }}" class="text-emerald-700 hover:text-emerald-800">
                            {{ $party->phone }}
                        </a>
                    </div>
                @endif

                @if($party->website)
                    <div class="flex items-center space-x-3">
                        <x-heroicon-o-globe-alt class="h-5 w-5 text-emerald-600 flex-shrink-0" />
                        
                        <a href="{{ $party->website }}" target="_blank" rel="noopener noreferrer" class="text-emerald-700 hover:text-emerald-800">
                            {{ $party->website }}
                        </a>
                    </div>
                @endif

                @if($party->groupLeader)
                    <div class="flex items-start space-x-3 md:col-span-2">
                        <x-heroicon-o-user-circle class="h-5 w-5 text-emerald-600 mt-0.5 flex-shrink-0" />
                        
                        <div class="text-gray-700">
                            <div class="font-medium">{!! __('Group Leader, City Council', 'fmr') !!}</div>
                            <div>{{ $party->groupLeader }}</div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>

@if($members->isNotEmpty())
    <div class="mt-6">
        <h2 class="text-2xl font-semibold mb-6">{!! __('Members', 'fmr') !!}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($members as $member)
                <a href="{{ get_permalink($member->ID) }}" class="group bg-white dark:bg-gray-100 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 p-4">
                    <div class="flex items-center space-x-4">
                        @if($member->thumbnail())
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 rounded-full overflow-hidden">
                                    {!! $member->thumbnail() !!}
                                </div>
                            </div>
                        @endif

                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-emerald-600 transition-colors duration-200">
                                @if($member->getMeta('person_firstname') && $member->getMeta('person_lastname'))
                                    {{ $member->getMeta('person_firstname') }} {{ $member->getMeta('person_lastname') }}
                                @else
                                    {{ $member->post_title }}
                                @endif
                            </h3>
                        </div>

                        <div class="flex-shrink-0">
                            <x-heroicon-o-arrow-right class="h-5 w-5 text-gray-400 group-hover:text-emerald-600 transition-colors duration-200" />
                        </div>
                    </div>
                </a>

            @endforeach
        </div>
    </div>
@endif
