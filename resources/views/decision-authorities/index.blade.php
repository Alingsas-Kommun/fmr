@extends('layouts.app')

@section('content')
    <div class="mt-3">
        <h1 class="text-3xl font-bold mb-8">{{ __('Decision Authorities', 'fmr') }}</h1>

        <div class="mb-3">
            <div class="py-4">
                <form action="{{ url()->current() }}" method="get" class="flex items-center gap-6">
                    <div class="flex items-center space-x-2">
                        <x-heroicon-o-funnel class="h-5 w-5 text-primary-600" />
                        <span class="text-sm font-medium text-gray-700">{{ __('Filters', 'fmr') }}:</span>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <label for="type" class="sr-only">{{ __('Type', 'fmr') }}</label>
                        <div class="relative">
                            <select name="type" id="type" class="appearance-none block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm dark:bg-gray-100" onchange="this.form.submit()">
                                <option value="">{{ __('All Types', 'fmr') }}</option>

                                @foreach($typeTerms as $typeTerm)
                                    <option value="{{ $typeTerm->name }}" {{ $filters['type'] === $typeTerm->name ? 'selected' : '' }}>{{ $typeTerm->name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <x-heroicon-o-chevron-down class="h-4 w-4 text-gray-400" />
                            </div>
                        </div>
                    </div>

                    @if($filters['type'])
                        <div class="flex-shrink-0">
                            <a href="{{ url()->current() }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                                <x-heroicon-o-x-mark class="h-4 w-4 mr-1" />
                                {{ __('Clear', 'fmr') }}
                            </a>
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
                            <span>{{ __('Title', 'fmr') }}</span>
                        </div>
                    </th>

                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-building-office-2 class="h-4 w-4" />
                            <span>{{ __('Board', 'fmr') }}</span>
                        </div>
                    </th>

                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-tag class="h-4 w-4" />
                            <span>{{ __('Type', 'fmr') }}</span>
                        </div>
                    </th>

                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-calendar class="h-4 w-4" />
                            <span>{{ __('Period', 'fmr') }}</span>
                        </div>
                    </th>
                </tr>
            </thead>

            <tbody class="bg-gray-50 dark:bg-gray-100 divide-y divide-gray-200">
                @forelse($decisionAuthorities as $authority)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-200 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <x-link href="{{ route('decision-authorities.show', $authority) }}" class="font-medium">
                                {{ $authority->title }}
                            </x-link>
                        </td>

                        <td class="px-6 py-4">
                            @if($authority->board)
                                <x-link href="{{ get_permalink($authority->board->ID) }}" class="font-medium">
                                    {{ $authority->board->post_title }}
                                </x-link>
                            @else
                                <span class="text-gray-400 italic">{{ __('No board assigned', 'fmr') }}</span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4">
                            @if($authority->typeTerm)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-500">
                                    {{ $authority->typeTerm->name }}
                                </span>
                            @else
                                <span class="text-gray-400 italic">{{ __('No type assigned', 'fmr') }}</span>
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                {{ $authority->start_date->format('j M Y') }} - {{ $authority->end_date->format('j M Y') }}
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-6 text-center text-gray-500">
                            <x-alert type="info">
                                {{ __('No decision authorities found.', 'fmr') }}
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
                    <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] - 1]) }}" 
                        class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        {{ __('Previous', 'fmr') }}
                    </a>
                @endif

                @for($i = 1; $i <= $pagination['last_page']; $i++)
                    @if($i == $pagination['current_page'])
                        <span class="px-3 py-2 text-sm font-medium text-white bg-primary-500 border border-primary-500 rounded-md">
                            {{ $i }}
                        </span>
                    @else
                        <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}" 
                            class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            {{ $i }}
                        </a>
                    @endif
                @endfor

                @if($pagination['current_page'] < $pagination['last_page'])
                    <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] + 1]) }}" 
                        class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        {{ __('Next', 'fmr') }}
                    </a>
                @endif
            </nav>
        </div>
    @endif
@endsection
