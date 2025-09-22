@extends('layouts.app')

@section('content')
    <div class="my-8">
        <a href="{{ route('decision-authorities.index') }}" class="text-emerald-600 hover:text-emerald-800">{{ __('Decision Authorities', 'fmr') }}</a>

        <span class="mx-2">/</span>

        <span class="text-gray-600">{{ $decisionAuthority->title }}</span>
    </div>

    <div class="md:bg-gray-50 dark:md:bg-gray-100 rounded-lg overflow-hidden md:p-8 mb-8">
        <div class="flex flex-col md:flex-row md:items-start space-y-4 md:space-y-0 md:space-x-6">
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $decisionAuthority->title }}</h1>
                
                <div class="flex flex-col sm:flex-row lg:flex-col xl:flex-row items-start space-y-3 sm:space-y-0 sm:space-x-6 lg:space-y-3 xl:space-y-0 xl:space-x-6">
                    <div class="flex items-center space-x-2">
                        <x-heroicon-o-building-office-2 class="h-5 w-5 text-emerald-600 flex-shrink-0" />
                        
                        <span class="sr-only">{{ __('Board', 'fmr') }}:</span>
                        
                        <a href="{{ get_permalink($decisionAuthority->board->ID) }}" class="text-emerald-700 hover:text-emerald-800 font-medium">
                            {{ $decisionAuthority->board->post_title }}
                        </a>
                    </div>

                    <div class="flex items-center space-x-2">                        
                        <span class="sr-only">{{ __('Type', 'fmr') }}:</span>
                        
                        <span class="inline-flex items-center px-3 py-1 gap-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">
                            <x-heroicon-o-tag class="h-4 w-4 text-emerald-600 flex-shrink-0" />
                            <span>{{ $decisionAuthority->type }}</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex-shrink-0">
                <div class="flex items-center space-x-2 mb-2">
                    <x-heroicon-o-calendar class="h-6 w-6 text-emerald-600 flex-shrink-0" />
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
        <div class="bg-white dark:bg-gray-100 rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold">{{ __('Active Assignments', 'fmr') }}</h2>
            </div>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 dark:bg-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Name', 'fmr') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Role', 'fmr') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Period', 'fmr') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Actions', 'fmr') }}
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white dark:bg-gray-100 divide-y divide-gray-200">
                    @forelse($activeAssignments as $assignment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <a href="{{ get_permalink($assignment->person->ID) }}" class="text-emerald-600 hover:text-emerald-900">
                                    {{ $assignment->person->post_title }}
                                </a>
                            </td>

                            <td class="px-6 py-4">
                                <a href="{{ route('assignments.index', ['role' => $assignment->roleTerm->slug]) }}" class="text-emerald-700 hover:text-emerald-800">
                                    {{ $assignment->roleTerm->name }}
                                </a>
                            </td>

                            <td class="px-6 py-4">
                                {{ date('j M Y', strtotime($assignment->period_start)) }} –
                                {{ date('j M Y', strtotime($assignment->period_end)) }}
                            </td>

                            <td class="px-6 py-4">
                                <a href="{{ route('assignments.show', $assignment) }}" class="text-emerald-600 hover:text-emerald-900">
                                    {{ __('View', 'fmr') }}
                                </a>
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
