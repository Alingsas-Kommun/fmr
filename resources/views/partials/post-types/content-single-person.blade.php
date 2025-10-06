@use('App\Utilities\TableColumn')

<div class="md:bg-primary-50 rounded-lg overflow-hidden md:p-8" x-data="{ showMoreInfo: false }">
    <div class="flex items-center space-x-6">
        @if($setting('show_person_image'))
            @if($person->image())
                <div class="flex-shrink-0">
                    <div class="w-30 h-30 flex items-center justify-center rounded-full overflow-hidden">
                        {!! $person->image('thumbnail', 'w-full h-full object-cover') !!}
                    </div>
                </div>
            @else
                <div class="w-30 h-30 bg-gray-50 md:bg-white rounded-full flex items-center justify-center flex-shrink-0">
                    <x-heroicon-o-user class="h-15 w-15 text-primary-600" />
                </div>
            @endif
        @endif
        
        <div class="flex-1">
            @if($person->party)
                <x-link href="{{ $person->party->url }}" class="flex items-center space-x-2 mb-1" :underline="false">
                    @if($person->party->image())
                        <div class="w-5 h-5 flex-shrink-0">
                            {!! $person->party->image('thumbnail', 'w-full h-full object-cover') !!}
                        </div>
                    @else
                        <x-heroicon-o-user-group class="h-5 w-5 text-primary-600 flex-shrink-0" />
                    @endif

                    <span>{{ $person->party->name }}</span>
                </x-link>
            @endif

            @if($person->meta->firstname && $person->meta->lastname)
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ $person->meta->firstname }} {{ $person->meta->lastname }}
                </h1>
            @endif
            
            @set($hasContactInfo, $person->meta->ssn || $person->meta->groupLeader)
            
            @if($hasContactInfo)
                <div class="flex items-center space-x-4 mt-2">
                    @if($person->meta->ssn)
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-identification class="h-6 w-6 text-primary-600 flex-shrink-0" />

                            <div class="text-gray-700">
                                <div class="sr-only">{!! __('Social Security Number', 'fmr') !!}</div>
                                <div>{{ $person->meta->ssn }}</div>
                            </div>
                        </div>
                    @endif

                    @if($person->meta->groupLeader)
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-user-circle class="h-6 w-6 text-primary-600 flex-shrink-0" />

                            <span class="text-gray-700">{!! __('Group Leader', 'fmr') !!}</span>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @set($hasBasicInfo, $person->meta->birthDate || $person->meta->listing)
    @set($hasHomeInfo, $person->meta->homeEmail || $person->meta->homePhone || $person->meta->homeMobile || $person->meta->homeWebpage || $person->meta->homeAddress || $person->meta->homeZip || $person->meta->homeCity || $person->meta->homeVisitingAddress)
    @set($hasWorkInfo, $person->meta->workEmail || $person->meta->workPhone || $person->meta->workMobile || $person->meta->workWebpage || $person->meta->workAddress || $person->meta->workZip || $person->meta->workCity || $person->meta->workVisitingAddress)
    @set($hasAdditionalInfo, $hasBasicInfo || $hasHomeInfo || $hasWorkInfo)
    
    @if($hasAdditionalInfo)
        <div class="flex items-center justify-between mt-6">
            <div class="flex-1 border-t border-gray-200"></div>

            <button @click="showMoreInfo = !showMoreInfo" class="mx-8 inline-flex items-center text-sm font-medium text-primary-500 hover:text-primary-600 transition-colors duration-200 focus:outline-none">
                <span x-text="showMoreInfo ? '{!! __('Hide Additional Information', 'fmr') !!}' : '{!! __('Show Additional Information', 'fmr') !!}'"></span>
                <x-heroicon-o-chevron-down class="ml-2 h-4 w-4 transition-transform duration-200" ::class="{ 'rotate-180': showMoreInfo }" />
            </button>
            
            <div class="flex-1 border-t border-gray-200"></div>
        </div>

        <div x-show="showMoreInfo" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-4"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-4"
            class="mt-6"
        >

            @if($hasBasicInfo)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 my-8">
                    @if($person->meta->birthDate)
                        <div class="flex items-start space-x-3">
                            <x-heroicon-o-calendar class="h-6 w-6 text-primary-600 flex-shrink-0 mt-0.5" />

                            <div class="text-gray-700">
                                <div class="font-bold">{!! __('Birth Date', 'fmr') !!}</div>
                                <div>{{ $person->meta->birthDate }}</div>
                            </div>
                        </div>
                    @endif

                    @if($person->meta->ssn)
                        <div class="flex items-start space-x-3">
                            <x-heroicon-o-identification class="h-6 w-6 text-primary-600 flex-shrink-0 mt-0.5" />

                            <div class="text-gray-700">
                                <div class="font-bold">{!! __('Social Security Number', 'fmr') !!}</div>
                                <div>{{ $person->meta->ssn }}</div>
                            </div>
                        </div>
                    @endif

                    @if($person->meta->listing)
                        <div class="flex items-start space-x-3">
                            <x-heroicon-o-list-bullet class="h-6 w-6 text-primary-600 flex-shrink-0 mt-0.5" />

                            <div class="text-gray-700">
                                <div class="font-bold">{!! __('Listing', 'fmr') !!}</div>
                                <div>{{ $person->meta->listing }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @if($hasHomeInfo)
                    <div class="bg-white rounded-lg border border-gray-200 dark:border-gray-300 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center pb-3 border-b border-gray-200 dark:border-gray-300">
                            <x-heroicon-o-home class="h-6 w-6 text-primary-600 mr-3" />
                            {!! __('Home Information', 'fmr') !!}
                        </h3>

                        <div class="space-y-5">
                            @if($person->meta->homeEmail)
                                <div class="flex items-start space-x-3">
                                    <x-heroicon-o-envelope class="h-6 w-6 text-primary-600 flex-shrink-0 mt-0.5" />
                                    
                                    <div class="text-gray-700">
                                        <div class="font-bold">{!! __('Email', 'fmr') !!}</div>
                                        <x-link href="mailto:{{ $person->meta->homeEmail }}">
                                            {{ $person->meta->homeEmail }}
                                        </x-link>
                                    </div>
                                </div>
                            @endif

                            @if($person->meta->homePhone)
                                <div class="flex items-start space-x-3">
                                    <x-heroicon-o-phone class="h-6 w-6 text-primary-600 flex-shrink-0 mt-0.5" />
                                    
                                    <div class="text-gray-700">
                                        <div class="font-bold">{!! __('Phone', 'fmr') !!}</div>
                                        <x-link href="tel:{{ $person->meta->homePhone }}">
                                            {{ $person->meta->homePhone }}
                                        </x-link>
                                    </div>
                                </div>
                            @endif

                            @if($person->meta->homeMobile)
                                <div class="flex items-start space-x-3">
                                    <x-heroicon-o-device-phone-mobile class="h-6 w-6 text-primary-600 flex-shrink-0 mt-0.5" />
                                    
                                    <div class="text-gray-700">
                                        <div class="font-bold">{!! __('Mobile', 'fmr') !!}</div>
                                        <x-link href="tel:{{ $person->meta->homeMobile }}">
                                            {{ $person->meta->homeMobile }}
                                        </x-link>
                                    </div>
                                </div>
                            @endif

                            @if($person->meta->homeWebpage)
                                <div class="flex items-start space-x-3">
                                    <x-heroicon-o-globe-alt class="h-6 w-6 text-primary-600 flex-shrink-0 mt-0.5" />
                                    
                                    <div class="text-gray-700">
                                        <div class="font-bold">{!! __('Website', 'fmr') !!}</div>
                                        <x-link href="{{ $person->meta->homeWebpage }}" target="_blank">
                                            {{ $person->meta->homeWebpage }}
                                        </x-link>
                                    </div>
                                </div>
                            @endif

                            @if($person->meta->homeAddress || $person->meta->homeZip || $person->meta->homeCity)
                                <div class="flex items-start space-x-3">
                                    <x-heroicon-o-map-pin class="h-5 w-5 text-primary-600 mt-0.5 flex-shrink-0" />
                                    
                                    <div class="text-gray-700">
                                        <div class="font-bold">{!! __('Address', 'fmr') !!}</div>
                                        @if($person->meta->homeAddress)
                                            <div>{{ $person->meta->homeAddress }}</div>
                                        @endif
                                        @if($person->meta->homeZip || $person->meta->homeCity)
                                            <div>
                                                @if($person->meta->homeZip){{ $person->meta->homeZip }}@endif
                                                @if($person->meta->homeZip && $person->meta->homeCity), @endif
                                                @if($person->meta->homeCity){{ $person->meta->homeCity }}@endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($person->meta->homeVisitingAddress)
                                <div class="flex items-start space-x-3">
                                    <x-heroicon-o-building-office class="h-5 w-5 text-primary-600 mt-0.5 flex-shrink-0" />
                                    <div class="text-gray-700">
                                        <div class="font-bold">{!! __('Visiting Address', 'fmr') !!}</div>
                                        <div>{{ $person->meta->homeVisitingAddress }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if($hasWorkInfo)
                    <div class="bg-white rounded-lg border border-gray-200 dark:border-gray-300 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center pb-3 border-b border-gray-200 dark:border-gray-300">
                            <x-heroicon-o-building-office-2 class="h-6 w-6 text-primary-600 mr-3" />
                            {!! __('Work Information', 'fmr') !!}
                        </h3>

                        <div class="space-y-5">
                            @if($person->meta->workEmail)
                                <div class="flex items-start space-x-3">
                                    <x-heroicon-o-envelope class="h-6 w-6 text-primary-600 flex-shrink-0 mt-0.5" />
                                    
                                    <div class="text-gray-700">
                                        <div class="font-bold">{!! __('Email', 'fmr') !!}</div>
                                        <x-link href="mailto:{{ $person->meta->workEmail }}">
                                            {{ $person->meta->workEmail }}
                                        </x-link>
                                    </div>
                                </div>
                            @endif

                            @if($person->meta->workPhone)
                                <div class="flex items-start space-x-3">
                                    <x-heroicon-o-phone class="h-6 w-6 text-primary-600 flex-shrink-0 mt-0.5" />
                                    
                                    <div class="text-gray-700">
                                        <div class="font-bold">{!! __('Phone', 'fmr') !!}</div>
                                        <x-link href="tel:{{ $person->meta->workPhone }}">
                                            {{ $person->meta->workPhone }}
                                        </x-link>
                                    </div>
                                </div>
                            @endif

                            @if($person->meta->workMobile)
                                <div class="flex items-start space-x-3">
                                    <x-heroicon-o-device-phone-mobile class="h-6 w-6 text-primary-600 flex-shrink-0 mt-0.5" />
                                    
                                    <div class="text-gray-700">
                                        <div class="font-bold">{!! __('Mobile', 'fmr') !!}</div>
                                        <x-link href="tel:{{ $person->meta->workMobile }}">
                                            {{ $person->meta->workMobile }}
                                        </x-link>
                                    </div>
                                </div>
                            @endif

                            @if($person->meta->workWebpage)
                                <div class="flex items-start space-x-3">
                                    <x-heroicon-o-globe-alt class="h-6 w-6 text-primary-600 flex-shrink-0 mt-0.5" />
                                    
                                    <div class="text-gray-700">
                                        <div class="font-bold">{!! __('Website', 'fmr') !!}</div>
                                        <x-link href="{{ $person->meta->workWebpage }}" target="_blank">
                                            {{ $person->meta->workWebpage }}
                                        </x-link>
                                    </div>
                                </div>
                            @endif

                            @if($person->meta->workAddress || $person->meta->workZip || $person->meta->workCity)
                                <div class="flex items-start space-x-3">
                                    <x-heroicon-o-map-pin class="h-6 w-6 text-primary-600 flex-shrink-0 mt-0.5" />
                                    
                                    <div class="text-gray-700">
                                        <div class="font-bold">{!! __('Address', 'fmr') !!}</div>
                                        @if($person->meta->workAddress)
                                            <div>{{ $person->meta->workAddress }}</div>
                                        @endif
                                        @if($person->meta->workZip || $person->meta->workCity)
                                            <div>
                                                @if($person->meta->workZip){{ $person->meta->workZip }}@endif
                                                @if($person->meta->workZip && $person->meta->workCity), @endif
                                                @if($person->meta->workCity){{ $person->meta->workCity }}@endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($person->meta->workVisitingAddress)
                                <div class="flex items-start space-x-3">
                                    <x-heroicon-o-building-office-2 class="h-6 w-6 text-primary-600 flex-shrink-0 mt-0.5" />
                                    
                                    <div class="text-gray-700">
                                        <div class="font-bold">{!! __('Visiting Address', 'fmr') !!}</div>
                                        <div>{{ $person->meta->workVisitingAddress }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>


@if(!empty($assignments))
    <div class="mt-5">
        <h2 class="text-xl font-semibold mb-4">{!! __('Assignments', 'fmr') !!}</h2>
        
        <div class="bg-white dark:bg-gray-100 rounded-lg border border-gray-200 dark:border-gray-300 overflow-hidden">
            @set($columns, [
                TableColumn::text('role', __('Role', 'fmr')),
                TableColumn::link('decisionAuthority.text', __('Decision Authority', 'fmr'), 'decisionAuthority.url', 'truncate max-w-60'),
                TableColumn::text('period', __('Period', 'fmr')),
                TableColumn::arrowLink('view.text', '', 'view.url')
            ])

            <x-sortable-table :data="$assignments" :columns="$columns" :empty-message="__('No assignments found.', 'fmr')" class="w-full" />
        </div>
    </div>
@else
    <div class="mt-5">
        <x-alert type="info">
            {{ __('No assignments found.', 'fmr') }}
        </x-alert>
    </div>
@endif