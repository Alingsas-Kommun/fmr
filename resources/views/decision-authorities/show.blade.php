@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <a href="{{ route('decision-authorities.index') }}" class="text-emerald-600 hover:text-emerald-800">{{ __('Decision Authorities', 'fmr') }}</a>

            <span class="mx-2">/</span>

            <span class="text-gray-600">{{ $decisionAuthority->title }}</span>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold mb-2">{{ $decisionAuthority->title }}</h1>

                    <div class="text-gray-600 mb-4">
                        <span class="font-medium">{{ __('Board', 'fmr') }}:</span> 
                        <a href="{{ get_permalink($decisionAuthority->board->ID) }}" class="text-emerald-600 hover:text-emerald-800">{{ $decisionAuthority->board->post_title }}</a>
                    </div>

                    <div class="text-gray-600 mb-4">
                        <span class="font-medium">{{ __('Type', 'fmr') }}:</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            {{ $decisionAuthority->type }}
                        </span>
                    </div>
                </div>

                <div class="text-right">
                    <div class="text-sm text-gray-600">{{ __('Period', 'fmr') }}</div>

                    <div class="font-medium">
                        {{ date('j M Y', strtotime($decisionAuthority->start_date)) }} –
                        {{ date('j M Y', strtotime($decisionAuthority->end_date)) }}
                    </div>
                </div>
            </div>
        </div>

        @if($activeAssignments->count() > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold">{{ __('Active Assignments', 'fmr') }}</h2>
                </div>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
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

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($activeAssignments as $assignment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <a href="{{ get_permalink($assignment->person->ID) }}" class="text-emerald-600 hover:text-emerald-900">
                                        {{ $assignment->person->post_title }}
                                    </a>
                                </td>

                                <td class="px-6 py-4">
                                    {{ $assignment->role }}
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
    </div>
@endsection
