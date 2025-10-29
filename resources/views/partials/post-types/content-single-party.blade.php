<div class="bg-primary-50 rounded-lg overflow-hidden">
    <div class="p-8">
        <div class="flex flex-wrap items-center gap-5">
            @if($party->image())
                <div class="flex-shrink-0">
                    <div class="w-24 h-24 flex items-center justify-center">
                        {!! $party->image('thumbnail', 'w-full h-full object-cover') !!}
                    </div>
                </div>
            @endif
            
            <div class="flex-1 space-y-1">
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ $party->name }}

                    @if($party->meta->shortening)
                        <span class="text-2xl font-normal text-gray-600">
                            ({{ $party->meta->shortening }})
                        </span>
                    @endif
                </h1>
                
                @if($party->meta->description)
                    <div class="prose max-w-none text-gray-700">
                        {!! wp_kses_post($party->meta->description) !!}
                    </div>
                @endif  
            </div>
        </div>

        @set($hasContactInfo, $party->meta->address || $party->meta->zip || $party->meta->city || $party->meta->email || $party->meta->phone || $party->meta->website || $groupLeader)

        @if($hasContactInfo)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-gray-200 mt-8 pt-6">
                @if($party->meta->address || $party->meta->zip || $party->meta->city)
                    <div class="flex items-start space-x-3">
                        <x-heroicon-o-map-pin class="h-6 w-6 text-primary-600 mt-0.5 flex-shrink-0" />
                        
                        <div class="text-gray-700">
                            @if($party->meta->address)
                                <div>{{ $party->meta->address }}</div>
                            @endif

                            @if($party->meta->zip || $party->meta->city)
                                <div>
                                    @if($party->meta->zip){{ $party->meta->zip }}@endif
                                    @if($party->meta->zip && $party->meta->city), @endif
                                    @if($party->meta->city){{ $party->meta->city }}@endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if($party->meta->email)
                    <div class="flex items-center space-x-3">
                        <x-heroicon-o-envelope class="h-6 w-6 text-primary-600 flex-shrink-0" />
                        
                        <x-link href="mailto:{{ $party->meta->email }}">
                            {{ $party->meta->email }}
                        </x-link>
                    </div>
                @endif

                @if($party->meta->phone)
                    <div class="flex items-center space-x-3">
                        <x-heroicon-o-phone class="h-6 w-6 text-primary-600 flex-shrink-0" />
                        
                        <x-link href="tel:{{ $party->meta->phone }}">
                            {{ $party->meta->phone }}
                        </x-link>
                    </div>
                @endif

                @if($party->meta->website)
                    <div class="flex items-center space-x-3">
                        <x-heroicon-o-globe-alt class="h-6 w-6 text-primary-600 flex-shrink-0" />

                        <x-link href="{{ $party->meta->website }}" target="_blank" rel="noopener noreferrer">
                            {{ $party->meta->website }}
                        </x-link>
                    </div>
                @endif

                @if($groupLeader)
                    <div class="flex items-start space-x-3 md:col-span-2">
                        <x-heroicon-o-user-circle class="h-6 w-6 text-primary-600 mt-0.5 flex-shrink-0" />
                        
                        <div class="text-gray-700">
                            <div class="font-medium">{!! __('Group Leader, City Council', 'fmr') !!}</div>
                            <div>
                                <x-link href="{{ $groupLeader->url }}">
                                    @if($groupLeader->meta->firstname && $groupLeader->meta->lastname)
                                        {{ $groupLeader->meta->firstname }} {{ $groupLeader->meta->lastname }}
                                    @else
                                        {{ $groupLeader->name }}
                                    @endif
                                </x-link>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>

<div class="mt-6">
    <h2 class="text-2xl font-semibold mb-6">{!! __('Active Members', 'fmr') !!}</h2>

    @if($activeMembers->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($activeMembers as $member)
                <a href="{{ $member->url }}" class="group bg-primary-50 rounded-lg duration-200 p-4">
                    <div class="flex items-center space-x-4">
                        @if($setting('show_person_image'))
                            @if($member->image())
                                <div class="flex-shrink-0">
                                    <div class="w-16 h-16 rounded-full overflow-hidden">
                                        {!! $member->image('thumbnail', 'w-full h-full object-cover') !!}
                                    </div>
                                </div>
                            @else
                                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center flex-shrink-0">
                                    <x-heroicon-o-user class="h-8 w-8 text-primary-600" />
                                </div>
                            @endif
                        @endif

                        <div class="flex-1 min-w-0">
                            <h3 class="text-md font-semibold text-gray-900 group-hover:text-primary-700 transition-colors duration-200">
                                @if($member->meta->firstname && $member->meta->lastname)
                                    {{ $member->meta->firstname }} {{ $member->meta->lastname }}
                                @else
                                    {{ $member->name }}
                                @endif
                            </h3>
                        </div>

                        <div class="flex-shrink-0">
                            <x-heroicon-o-arrow-right class="h-5 w-5 text-gray-400 group-hover:text-primary-700 transition-colors duration-200" />
                        </div>
                    </div>
                </a>

            @endforeach
        </div>
    @else
        <x-alert type="info">
            {!! __('No members found for this party.', 'fmr') !!}
        </x-alert>
    @endif
</div>

@user
    <div class="mt-6">
        <h3 class="text-2xl font-bold mb-6">{!! __('Inactive Members', 'fmr') !!}</h3>
        
        @if($inactiveMembers->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($inactiveMembers as $member)
                    <a href="{!! $member->editUrl() !!}" target="_blank" class="flex items-center space-x-4 bg-gray-100 rounded-lg p-4">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-md font-normal text-gray-900 transition-colors duration-200">
                                {!! $member->name !!}
                            </h3>
                        </div>

                        <div class="flex-shrink-0">
                            <x-heroicon-o-arrow-right class="h-5 w-5 text-gray-400 group-hover:text-primary-700 transition-colors duration-200" />
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <x-alert type="info">
                {!! __('No inactive members found for this party.', 'fmr') !!}
            </x-alert>
        @endif
    </div>
@enduser