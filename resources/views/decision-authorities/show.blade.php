@extends('layouts.app')

@use('App\Utilities\TableColumn')

@section('content')
    <div class="md:bg-primary-50 rounded-lg overflow-hidden md:p-8 mt-3 mb-3">
        <div class="flex flex-col md:flex-row md:items-start space-y-4 md:space-y-0 md:space-x-6">
            <div class="flex-1">
                <div class="flex items-center space-x-2 mb-2">
                    <x-heroicon-o-building-office-2 class="h-5 w-5 text-primary-600 flex-shrink-0" />
                    
                    <span class="sr-only">{{ __('Board', 'fmr') }}:</span>
                    
                    <x-link href="{{ get_permalink($decisionAuthority->board->ID) }}" :underline="false" class="font-medium">
                        {{ $decisionAuthority->board->post_title }}
                    </x-link>
                </div>

                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $decisionAuthority->title }}</h1>
                
                <div class="flex items-center space-x-2">                        
                    <span class="sr-only">{{ __('Type', 'fmr') }}:</span>
                    
                    @if($decisionAuthority->typeTerm)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-primary-100 text-primary-500 space-x-1">
                            <x-heroicon-o-tag class="h-4 w-4 text-primary-600 flex-shrink-0" />
                            <span>{{ $decisionAuthority->typeTerm->name }}</span>
                        </span>
                    @else
                        <span class="text-gray-400 italic">{{ __('No type assigned', 'fmr') }}</span>
                    @endif
                </div>
            </div>

            <div class="flex-shrink-0">
                <div class="flex items-center space-x-2 mb-2">
                    <x-heroicon-o-calendar class="h-6 w-6 text-primary-600 flex-shrink-0" />
                    <span class="sr-only">{{ __('Period', 'fmr') }}</span>
                    <div class="text-lg font-semibold text-gray-900">
                        {{ date('j M Y', strtotime($decisionAuthority->start_date)) }} –
                        {{ date('j M Y', strtotime($decisionAuthority->end_date)) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(! empty($assignments))
        <div class="py-4">
            <h2 class="text-xl font-semibold">{{ __('Active Assignments', 'fmr') }}</h2>
        </div>

        <div class="bg-white dark:bg-gray-100 rounded-lg border border-gray-200">
            @set($columns, [
                TableColumn::link('person.text', __('Name', 'fmr'), 'person.url', 'truncate max-w-60'),
                TableColumn::badge('role', __('Role', 'fmr')),
                TableColumn::text('period', __('Period', 'fmr')),
                TableColumn::arrowLink('view.text', '', 'view.url')
            ])

            <x-sortable-table :data="$assignments" :columns="$columns" :empty-message="__('No active assignments found.', 'fmr')" class="w-full" />
        </div>
    @else
        <x-alert type="info">
            {{ __('No active assignments found.', 'fmr') }}
        </x-alert>
    @endif
@endsection
