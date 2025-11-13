@extends('layouts.app')

@use('App\Utilities\TableColumn')

@section('content')
    <div class="bg-primary-50 rounded-lg overflow-hidden p-8 mt-3 mb-3">
        <div class="flex flex-col md:flex-row md:items-start space-y-4 md:space-y-0 md:space-x-6">
            <div class="flex-1">
                <div class="flex items-center space-x-2 mb-2">
                    <x-heroicon-o-building-office-2 class="h-5 w-5 text-primary-600 flex-shrink-0" />
                    
                    <span class="sr-only">{{ __('Board', 'fmr') }}:</span>
                    
                    <x-link href="{{ get_permalink($decisionAuthority->board->ID) }}" :underline="false" class="font-medium">
                        {{ $decisionAuthority->board->post_title }}
                    </x-link>
                </div>

                <h1 class="text-3xl font-bold text-gray-900">{{ $decisionAuthority->title }}</h1>

                @if($decisionAuthority->board->categoryTerm)
                    <div class="flex items-center space-x-2 mt-4">                        
                        <span class="sr-only">{{ __('Type', 'fmr') }}:</span>
                        
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-primary-100 text-primary-500 space-x-1">
                            <x-heroicon-o-tag class="h-4 w-4 text-primary-600 flex-shrink-0" />
                            <span>{{ $decisionAuthority->board->categoryTerm->name }}</span>
                        </span>  
                    </div>
                @endif
            </div>

            <div class="flex-shrink-0 md:ml-auto border-t md:border-t-0 border-gray-200 dark:border-gray-300 pt-6 md:pt-0 mt-3 md:mt-0">
                <div class="flex items-center space-x-2 mb-2">
                    <x-heroicon-o-calendar class="h-5 w-5 text-primary-600 flex-shrink-0" />
                    <span class="text-sm font-medium text-gray-700">{{ __('Period', 'fmr') }}</span>
                </div>

                <div class="text-lg font-semibold text-gray-900">
                    {!! sprintf('%s - %s', $decisionAuthority->start_date->formatDate(), $decisionAuthority->end_date->formatDate()) !!}
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
                TableColumn::text('role', __('Role', 'fmr')),
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
