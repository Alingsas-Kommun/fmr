<div class="md:bg-gray-50 dark:md:bg-gray-100 rounded-lg overflow-hidden md:p-8" x-data="{ showMoreInfo: false }">
    <div class="flex items-center space-x-6">
        @if($thumbnail)
            <div class="flex-shrink-0">
                <div class="w-30 h-30 flex items-center justify-center rounded-full overflow-hidden">
                    {!! $thumbnail !!}
                </div>
            </div>
        @else
            <div class="w-30 h-30 bg-gray-50 md:bg-white rounded-full flex items-center justify-center flex-shrink-0">
                <x-heroicon-o-user class="h-15 w-15 text-emerald-600" />
            </div>
        @endif
        
        <div class="flex-1">
            @if($party)
                <a href="{{ get_permalink($party->ID) }}" class="flex items-center space-x-2 mb-1 text-gray-800 hover:text-gray-900">
                    @if($party->thumbnail())
                        <div class="w-5 h-5 flex-shrink-0">
                            {!! $party->thumbnail('w-5 h-5') !!}
                        </div>
                    @else
                        <x-heroicon-o-user-group class="h-5 w-5 text-emerald-600 flex-shrink-0" />
                    @endif

                    <span>{{ $party->post_title }}</span>
                </a>
            @endif

            @if($person->firstname && $person->lastname)
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ $person->firstname }} {{ $person->lastname }}
                </h2>
            @endif
            
            @set($hasContactInfo, $person->workEmail || $person->groupLeader)
            
            @if($hasContactInfo)
                <div class="flex items-center space-x-4 mt-2">
                    @if($person->workEmail)
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-envelope class="h-6 w-6 text-emerald-600 flex-shrink-0" />

                            <a href="mailto:{{ $person->workEmail }}" class="text-gray-800 hover:text-gray-900">
                                {{ $person->workEmail }}
                            </a>
                        </div>
                    @endif

                    @if($person->groupLeader)
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-user-circle class="h-6 w-6 text-emerald-600 flex-shrink-0" />

                            <span class="text-gray-700">{!! __('Group Leader', 'fmr') !!}</span>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @set($hasBasicInfo, $person->birthDate || $person->ssn || $person->listing)

    @if($hasBasicInfo)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 border-t border-gray-200 pt-6 mt-8">
            @if($person->birthDate)
                <div class="flex items-start space-x-3">
                    <x-heroicon-o-calendar class="h-5 w-5 text-emerald-600 flex-shrink-0" />

                    <div class="text-gray-700">
                        <div class="font-bold">{!! __('Birth Date', 'fmr') !!}</div>
                        <div>{{ $person->birthDate }}</div>
                    </div>
                </div>
            @endif

            @if($person->ssn)
                <div class="flex items-start space-x-3">
                    <x-heroicon-o-identification class="h-5 w-5 text-emerald-600 flex-shrink-0" />

                    <div class="text-gray-700">
                        <div class="font-bold">{!! __('Social Security Number', 'fmr') !!}</div>
                        <div>{{ $person->ssn }}</div>
                    </div>
                </div>
            @endif

            @if($person->listing)
                <div class="flex items-start space-x-3">
                    <x-heroicon-o-list-bullet class="h-5 w-5 text-emerald-600 flex-shrink-0" />

                    <div class="text-gray-700">
                        <div class="font-bold">{!! __('Listing', 'fmr') !!}</div>
                        <div>{{ $person->listing }}</div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    @set($hasHomeInfo, $person->homeEmail || $person->homePhone || $person->homeMobile || $person->homeWebpage || $person->homeAddress || $person->homeZip || $person->homeCity || $person->homeVisitingAddress)
    @set($hasWorkInfo, $person->workEmail || $person->workPhone || $person->workMobile || $person->workWebpage || $person->workAddress || $person->workZip || $person->workCity || $person->workVisitingAddress)
    @set($hasAdditionalInfo, $hasHomeInfo || $hasWorkInfo)
    
    @if($hasAdditionalInfo)
        <div class="flex items-center justify-between mt-6">
            <div class="flex-1 border-t border-gray-200"></div>

             <button @click="showMoreInfo = !showMoreInfo" class="mx-8 inline-flex items-center text-sm font-medium text-emerald-700 hover:text-emerald-800 transition-colors duration-200 focus:outline-none">
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
            class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6"
        >
            
            @if($hasHomeInfo)
                <div class="bg-white dark:bg-gray-200 rounded-lg border border-gray-200 dark:border-gray-300 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center pb-3 border-b border-gray-200 dark:border-gray-300">
                        <x-heroicon-o-home class="h-5 w-5 text-emerald-600 mr-3" />
                        {!! __('Home Information', 'fmr') !!}
                    </h3>

                    <div class="space-y-5">
                        @if($person->homeEmail)
                            <div class="flex items-start space-x-3">
                                <x-heroicon-o-envelope class="h-5 w-5 text-emerald-600 flex-shrink-0" />
                                
                                <div class="text-gray-700">
                                    <div class="font-bold">{!! __('Email', 'fmr') !!}</div>
                                    <a href="mailto:{{ $person->homeEmail }}" class="text-emerald-700 hover:text-emerald-800">
                                        {{ $person->homeEmail }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if($person->homePhone)
                            <div class="flex items-start space-x-3">
                                <x-heroicon-o-phone class="h-5 w-5 text-emerald-600 flex-shrink-0" />
                                
                                <div class="text-gray-700">
                                    <div class="font-bold">{!! __('Phone', 'fmr') !!}</div>
                                    <a href="tel:{{ $person->homePhone }}" class="text-emerald-700 hover:text-emerald-800">
                                        {{ $person->homePhone }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if($person->homeMobile)
                            <div class="flex items-start space-x-3">
                                <x-heroicon-o-device-phone-mobile class="h-5 w-5 text-emerald-600 flex-shrink-0" />
                                
                                <div class="text-gray-700">
                                    <div class="font-bold">{!! __('Mobile', 'fmr') !!}</div>
                                    <a href="tel:{{ $person->homeMobile }}" class="text-emerald-700 hover:text-emerald-800">
                                        {{ $person->homeMobile }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if($person->homeWebpage)
                            <div class="flex items-start space-x-3">
                                <x-heroicon-o-globe-alt class="h-5 w-5 text-emerald-600 flex-shrink-0" />
                                
                                <div class="text-gray-700">
                                    <div class="font-bold">{!! __('Website', 'fmr') !!}</div>
                                    <a href="{{ $person->homeWebpage }}" target="_blank" rel="noopener noreferrer" class="text-emerald-700 hover:text-emerald-800">
                                        {{ $person->homeWebpage }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if($person->homeAddress || $person->homeZip || $person->homeCity)
                            <div class="flex items-start space-x-3">
                                <x-heroicon-o-map-pin class="h-5 w-5 text-emerald-600 mt-0.5 flex-shrink-0" />
                                
                                <div class="text-gray-700">
                                    <div class="font-bold">{!! __('Address', 'fmr') !!}</div>
                                    @if($person->homeAddress)
                                        <div>{{ $person->homeAddress }}</div>
                                    @endif
                                    @if($person->homeZip || $person->homeCity)
                                        <div>
                                            @if($person->homeZip){{ $person->homeZip }}@endif
                                            @if($person->homeZip && $person->homeCity), @endif
                                            @if($person->homeCity){{ $person->homeCity }}@endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($person->homeVisitingAddress)
                            <div class="flex items-start space-x-3">
                                <x-heroicon-o-building-office class="h-5 w-5 text-emerald-600 mt-0.5 flex-shrink-0" />
                                <div class="text-gray-700">
                                    <div class="font-bold">{!! __('Visiting Address', 'fmr') !!}</div>
                                    <div>{{ $person->homeVisitingAddress }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if($hasWorkInfo)
                <div class="bg-white dark:bg-gray-200 rounded-lg border border-gray-200 dark:border-gray-300 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center pb-3 border-b border-gray-200 dark:border-gray-300">
                        <x-heroicon-o-building-office-2 class="h-5 w-5 text-emerald-600 mr-3" />
                        {!! __('Work Information', 'fmr') !!}
                    </h3>

                    <div class="space-y-5">
                        @if($person->workEmail)
                            <div class="flex items-start space-x-3">
                                <x-heroicon-o-envelope class="h-5 w-5 text-emerald-600 flex-shrink-0" />
                                
                                <div class="text-gray-700">
                                    <div class="font-bold">{!! __('Email', 'fmr') !!}</div>
                                    <a href="mailto:{{ $person->workEmail }}" class="text-emerald-700 hover:text-emerald-800">
                                        {{ $person->workEmail }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if($person->workPhone)
                            <div class="flex items-start space-x-3">
                                <x-heroicon-o-phone class="h-5 w-5 text-emerald-600 flex-shrink-0" />
                                
                                <div class="text-gray-700">
                                    <div class="font-bold">{!! __('Phone', 'fmr') !!}</div>
                                    <a href="tel:{{ $person->workPhone }}" class="text-emerald-700 hover:text-emerald-800">
                                        {{ $person->workPhone }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if($person->workMobile)
                            <div class="flex items-start space-x-3">
                                <x-heroicon-o-device-phone-mobile class="h-5 w-5 text-emerald-600 flex-shrink-0" />
                                
                                <div class="text-gray-700">
                                    <div class="font-bold">{!! __('Mobile', 'fmr') !!}</div>
                                    <a href="tel:{{ $person->workMobile }}" class="text-emerald-700 hover:text-emerald-800">
                                        {{ $person->workMobile }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if($person->workWebpage)
                            <div class="flex items-start space-x-3">
                                <x-heroicon-o-globe-alt class="h-5 w-5 text-emerald-600 flex-shrink-0" />
                                
                                <div class="text-gray-700">
                                    <div class="font-bold">{!! __('Website', 'fmr') !!}</div>
                                    <a href="{{ $person->workWebpage }}" target="_blank" rel="noopener noreferrer" class="text-emerald-700 hover:text-emerald-800">
                                        {{ $person->workWebpage }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if($person->workAddress || $person->workZip || $person->workCity)
                            <div class="flex items-start space-x-3">
                                <x-heroicon-o-map-pin class="h-5 w-5 text-emerald-600 mt-0.5 flex-shrink-0" />
                                
                                <div class="text-gray-700">
                                    <div class="font-bold">{!! __('Address', 'fmr') !!}</div>
                                    @if($person->workAddress)
                                        <div>{{ $person->workAddress }}</div>
                                    @endif
                                    @if($person->workZip || $person->workCity)
                                <div>

                                @if($person->workZip){{ $person->workZip }}@endif
                                @if($person->workZip && $person->workCity), @endif
                                @if($person->workCity){{ $person->workCity }}@endif
                            </div>
                        @endif
                    </div>
                    </div>
                        @endif

                        @if($person->workVisitingAddress)
                            <div class="flex items-start space-x-3">
                                <x-heroicon-o-building-office-2 class="h-5 w-5 text-emerald-600 mt-0.5 flex-shrink-0" />
                                
                                <div class="text-gray-700">
                                    <div class="font-bold">{!! __('Visiting Address', 'fmr') !!}</div>
                                    <div>{{ $person->workVisitingAddress }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>


<div class="my-6">
    <h2 class="text-2xl font-semibold mb-4">{!! __('Assignments', 'fmr') !!}</h2>
    
    @if($assignments->isNotEmpty())
        <div class="bg-gray-50 dark:bg-gray-100 rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 dark:bg-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! __('Role', 'fmr') !!}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! __('Board', 'fmr') !!}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! __('Decision Authority', 'fmr') !!}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! __('Period', 'fmr') !!}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! __('Actions', 'fmr') !!}</th>
                    </tr>
                </thead>

                <tbody class="bg-white dark:bg-gray-100 divide-y divide-gray-200">
                    @foreach($assignments as $assignment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('assignments.index', ['role' => $assignment->roleTerm->slug]) }}" class="text-emerald-700 hover:text-emerald-800">
                                    {{ $assignment->roleTerm->name }}
                                </a>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($assignment->board)
                                    <a href="{{ get_permalink($assignment->board->ID) }}" class="text-emerald-700 hover:text-emerald-800"> 
                                        {{ $assignment->board->post_title }}
                                    </a>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($assignment->decisionAuthority)
                                    <a href="{{ route('decision-authorities.show', $assignment->decisionAuthority) }}" class="text-emerald-700 hover:text-emerald-800">
                                        {{ $assignment->decisionAuthority->title }}
                                    </a>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $assignment->period_start->format('Y-m-d') }} - {{ $assignment->period_end->format('Y-m-d') }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('assignments.show', $assignment) }}" class="text-emerald-700 hover:text-emerald-800">{!! __('View', 'fmr') !!}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <x-alert type="info">
            {{ __('No assignments found.', 'fmr') }}
        </x-alert>
    @endif
</div>
