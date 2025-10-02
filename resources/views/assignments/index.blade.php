@extends('layouts.app')

@section('content')
    <div class="mt-3">
        <h1 class="text-3xl font-bold mb-8">{{ __('Assignments', 'fmr') }}</h1>

        <div class="mb-3">
            <div class="py-4">
                <form action="{{ url()->current() }}" method="get" class="flex items-center gap-6">
                    <div class="flex items-center space-x-2">
                        <x-heroicon-o-funnel class="h-5 w-5 text-primary-600" />
                        <span class="text-sm font-medium text-gray-700">{{ __('Filters', 'fmr') }}:</span>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <label for="role" class="sr-only">{{ __('Role', 'fmr') }}</label>
                        <div class="relative">
                            <select name="role" id="role" class="appearance-none block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm dark:bg-gray-100" onchange="this.form.submit()">
                                <option value="">{{ __('All roles', 'fmr') }}</option>
                                @foreach($roleTerms as $roleTerm)
                                    <option value="{{ $roleTerm->slug }}" {{ $filters['role'] === $roleTerm->slug ? 'selected' : '' }}>
                                        {{ $roleTerm->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <x-heroicon-o-chevron-down class="h-4 w-4 text-gray-400" />
                            </div>
                        </div>
                    </div>

                    @if($filters['role'])
                        <div class="flex-shrink-0">
                            <x-link href="{{ url()->current() }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200" :underline="false">
                                <x-heroicon-o-x-mark class="h-4 w-4 mr-1" />
                                {{ __('Clear', 'fmr') }}
                            </x-link>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-100 rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100 dark:bg-gray-200">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-document-text class="h-4 w-4" />
                            <span>{{ __('Decision Authority', 'fmr') }}</span>
                        </div>
                    </th>

                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-user class="h-4 w-4" />
                            <span>{{ __('Person', 'fmr') }}</span>
                        </div>
                    </th>

                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-tag class="h-4 w-4" />
                            <span>{{ __('Role', 'fmr') }}</span>
                        </div>
                    </th>

                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-calendar class="h-4 w-4" />
                            <span>{{ __('Period', 'fmr') }}</span>
                        </div>
                    </th>

                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider sr-only">
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-eye class="h-4 w-4" />
                            <span>{{ __('Show', 'fmr') }}</span>
                        </div>
                    </th>
                </tr>
            </thead>

            <tbody class="bg-gray-50 dark:bg-gray-100 divide-y divide-gray-200">
                @forelse($assignments as $assignment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-200 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($assignment->decisionAuthority)
                                <x-link href="{{ route('decision-authorities.show', $assignment->decisionAuthority) }}">
                                    {{ $assignment->decisionAuthority->title }}
                                </x-link>
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($assignment->person)
                                <x-link href="{{ get_permalink($assignment->person->ID) }}">
                                    {{ $assignment->person->post_title }}
                                </x-link>
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-500">
                                {{ $assignment->roleTerm->name }}
                            </span>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $assignment->period_start->format('Y-m-d') }} - {{ $assignment->period_end->format('Y-m-d') }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-link href="{{ route('assignments.show', $assignment) }}" class="inline-flex items-center font-medium flex space-x-1">
                                <span>{!! __('View', 'fmr') !!}</span>
                                <x-heroicon-o-arrow-right class="h-4 w-4 mr-1" />
                            </x-link>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-6 text-center text-gray-500">
                            <x-alert type="info">
                                {{ __('No assignments found.', 'fmr') }}
                            </x-alert>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pagination['last_page'] > 1)
        <div class="mt-6 flex justify-center">
            <nav class="flex space-x-2">
                @if($pagination['current_page'] > 1)
                    <x-link href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] - 1]) }}" 
                        class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50" :underline="false">
                        {{ __('Previous', 'fmr') }}
                    </x-link>
                @endif

                @for($i = 1; $i <= $pagination['last_page']; $i++)
                    @if($i == $pagination['current_page'])
                        <span class="px-3 py-2 text-sm font-medium text-white bg-primary-500 border border-primary-500 rounded-md">
                            {{ $i }}
                        </span>
                    @else
                        <x-link href="{{ request()->fullUrlWithQuery(['page' => $i]) }}" 
                            class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50" :underline="false">
                            {{ $i }}
                        </x-link>
                    @endif
                @endfor

                @if($pagination['current_page'] < $pagination['last_page'])
                    <x-link href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] + 1]) }}" 
                        class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50" :underline="false">
                        {{ __('Next', 'fmr') }}
                    </x-link>
                @endif
            </nav>
        </div>
    @endif
@endsection
