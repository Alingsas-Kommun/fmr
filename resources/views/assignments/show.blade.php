@extends('layouts.app')

@section('content')
    <div class="bg-primary-50 rounded-lg overflow-hidden p-8 mt-3">
        <div class="flex flex-col md:flex-row md:items-start space-y-4 md:space-y-0 mb-8">
            <div class="flex items-center space-x-6 flex-1">
                <div class="flex-shrink-0">
                    <div class="w-20 h-20 bg-primary-100 dark:bg-primary-200 rounded-lg flex items-center justify-center">
                        <x-heroicon-o-briefcase class="h-8 w-8 text-primary-600" />
                    </div>
                </div>
                
                <div class="flex-1 space-y-2">
                    <h1 class="text-2xl font-bold text-gray-900">{!! $assignment->roleTerm->name !!}</h1>
                    
                    @if($assignment->person)
                        <div class="flex items-center space-x-2">
                            @if($setting('show_person_image'))
                                @if($assignment->person->thumbnail())
                                    <div class="w-6 h-6 rounded-full overflow-hidden flex-shrink-0">
                                        {!! $assignment->person->thumbnail() !!}
                                    </div>
                                @else
                                    <div class="w-6 h-6 bg-white rounded-full flex items-center justify-center flex-shrink-0">
                                        <x-heroicon-o-user class="w-4 h-4 text-primary-600" />
                                    </div>
                                @endif
                            @endif
                            
                            <x-link href="{{ get_permalink($assignment->person->ID) }}" :underline="false" class="font-medium">
                                {{ $assignment->person->post_title }}
                            </x-link>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex-shrink-0 md:ml-auto border-t md:border-t-0 border-gray-200 dark:border-gray-300 pt-6 md:pt-0 mt-3 md:mt-0">
                <div class="flex items-center space-x-2 mb-2">
                    <x-heroicon-o-calendar class="h-5 w-5 text-primary-600 flex-shrink-0" />
                    <span class="text-sm font-medium text-gray-700">{{ __('Period', 'fmr') }}</span>
                </div>
                
                <div class="text-lg font-semibold text-gray-900">
                    {!! sprintf('%s - %s', $assignment->period_start->formatDate(), $assignment->period_end->formatDate()) !!}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-100 rounded-lg border border-gray-200 dark:border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center pb-3 border-b border-gray-200 dark:border-gray-300">
                    <x-heroicon-o-building-office-2 class="h-5 w-5 text-primary-600 mr-3" />
                    {!! __('Board', 'fmr') !!}
                </h3>
                
                @if($assignment->board)
                    <x-link href="{{ get_permalink($assignment->board->ID) }}" class="font-medium">
                        {{ $assignment->board->post_title }}
                    </x-link>
                @else
                    <span class="text-gray-400 italic">{{ __('No board assigned', 'fmr') }}</span>
                @endif
            </div>

            <div class="bg-white dark:bg-gray-100 rounded-lg border border-gray-200 dark:border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center pb-3 border-b border-gray-200 dark:border-gray-300">
                    <x-heroicon-o-scale class="h-5 w-5 text-primary-600 mr-3" />
                    {!! __('Decision Authority', 'fmr') !!}
                </h3>
                
                @if($assignment->decisionAuthority)
                    <x-link href="{{ route('decision-authorities.show', $assignment->decisionAuthority) }}" class="font-medium">
                        {{ $assignment->decisionAuthority->title }}
                    </x-link>
                @else
                    <span class="text-gray-400 italic">{{ __('No decision authority assigned', 'fmr') }}</span>
                @endif
            </div>
        </div>
    </div>
@endsection
