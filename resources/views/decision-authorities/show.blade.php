@extends('layouts.app')

@section('content')


    <div class="md:bg-primary-50 rounded-lg overflow-hidden md:p-8 my-8">
        <div class="flex flex-col md:flex-row md:items-start space-y-4 md:space-y-0 md:space-x-6">
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $decisionAuthority->title }}</h1>
                
                <div class="flex flex-col sm:flex-row lg:flex-col xl:flex-row items-start space-y-3 sm:space-y-0 sm:space-x-6 lg:space-y-3 xl:space-y-0 xl:space-x-6">
                    <div class="flex items-center space-x-2">
                        <x-heroicon-o-building-office-2 class="h-5 w-5 text-primary-600 flex-shrink-0" />
                        
                        <span class="sr-only">{{ __('Board', 'fmr') }}:</span>
                        
                        <x-link href="{{ get_permalink($decisionAuthority->board->ID) }}" class="font-medium">
                            {{ $decisionAuthority->board->post_title }}
                        </x-link>
                    </div>

                    <div class="flex items-center space-x-2">                        
                        <span class="sr-only">{{ __('Type', 'fmr') }}:</span>
                        
                        @if($decisionAuthority->typeTerm)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-white text-primary-500 space-x-1">
                                <x-heroicon-o-tag class="h-4 w-4 text-primary-600 flex-shrink-0" />
                                <span>{{ $decisionAuthority->typeTerm->name }}</span>
                            </span>
                        @else
                            <span class="text-gray-400 italic">{{ __('No type assigned', 'fmr') }}</span>
                        @endif
                    </div>
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

    @if($activeAssignments->count() > 0)
        <div class="py-4">
            <h2 class="text-xl font-semibold">{{ __('Active Assignments', 'fmr') }}</h2>
        </div>

        <div class="bg-white dark:bg-gray-100 rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100 dark:bg-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                            {{ __('Name', 'fmr') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                            {{ __('Role', 'fmr') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                            {{ __('Period', 'fmr') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                            {{ __('Actions', 'fmr') }}
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-gray-50 dark:bg-gray-100 divide-y divide-gray-200">
                    @forelse($activeAssignments as $assignment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <x-link href="{{ get_permalink($assignment->person->ID) }}">
                                    {{ $assignment->person->post_title }}
                                </x-link>
                            </td>

                            <td class="px-6 py-4">
                                <x-link href="{{ route('assignments.index', ['role' => $assignment->roleTerm->slug]) }}">
                                    {{ $assignment->roleTerm->name }}
                                </x-link>
                            </td>

                            <td class="px-6 py-4">
                                {{ date('j M Y', strtotime($assignment->period_start)) }} –
                                {{ date('j M Y', strtotime($assignment->period_end)) }}
                            </td>

                            <td class="px-6 py-4">
                                <x-link href="{{ route('assignments.show', $assignment) }}">
                                    {{ __('View', 'fmr') }}
                                </x-link>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                                {{ __('No active assignments found.', 'fmr') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <x-alert type="info">
            {{ __('No active assignments found.', 'fmr') }}
        </x-alert>
    @endif
@endsection
