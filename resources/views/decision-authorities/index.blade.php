@extends('layouts.app')

@section('content')
    <div class="mt-8">
        <h1 class="text-3xl font-bold mb-8">{{ __('Decision Authorities', 'fmr') }}</h1>

        <div class="mb-3">
            <div class="py-4">
                <form action="{{ url()->current() }}" method="get" class="flex items-center gap-6">
                    <div class="flex items-center space-x-2">
                        <x-heroicon-o-funnel class="h-5 w-5 text-emerald-600" />
                        <span class="text-sm font-medium text-gray-700">{{ __('Filters', 'fmr') }}:</span>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <label for="type" class="sr-only">{{ __('Type', 'fmr') }}</label>
                        <div class="relative">
                            <select name="type" id="type" class="appearance-none block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm bg-white" onchange="this.form.submit()">
                                <option value="">{{ __('All Types', 'fmr') }}</option>

                                @foreach(['Nämnd', 'Styrelse', 'Utskott', 'Beredning', 'Råd'] as $type)
                                    <option value="{{ $type }}" {{ $filters['type'] === $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <x-heroicon-o-chevron-down class="h-4 w-4 text-gray-400" />
                            </div>
                        </div>
                    </div>

                    @if($filters['type'])
                        <div class="flex-shrink-0">
                            <a href="{{ url()->current() }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200">
                                <x-heroicon-o-x-mark class="h-4 w-4 mr-1" />
                                {{ __('Clear', 'fmr') }}
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-100 rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 dark:bg-gray-200">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-document-text class="h-4 w-4" />
                            <span>{{ __('Title', 'fmr') }}</span>
                        </div>
                    </th>

                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-building-office-2 class="h-4 w-4" />
                            <span>{{ __('Board', 'fmr') }}</span>
                        </div>
                    </th>

                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-tag class="h-4 w-4" />
                            <span>{{ __('Type', 'fmr') }}</span>
                        </div>
                    </th>

                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-calendar class="h-4 w-4" />
                            <span>{{ __('Period', 'fmr') }}</span>
                        </div>
                    </th>
                </tr>
            </thead>

            <tbody class="bg-white dark:bg-gray-100 divide-y divide-gray-200">
                @forelse($decisionAuthorities as $authority)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-200 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <a href="{{ route('decision-authorities.show', $authority) }}" class="text-emerald-700 hover:text-emerald-800 font-medium">
                                {{ $authority->title }}
                            </a>
                        </td>

                        <td class="px-6 py-4">
                            @if($authority->board)
                                <a href="{{ get_permalink($authority->board->ID) }}" class="text-emerald-700 hover:text-emerald-800 font-medium">
                                    {{ $authority->board->post_title }}
                                </a>
                            @else
                                <span class="text-gray-400 italic">{{ __('No board assigned', 'fmr') }}</span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                {{ $authority->type }}
                            </span>
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
                        class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        {{ __('Previous', 'fmr') }}
                    </a>
                @endif

                @for($i = 1; $i <= $pagination['last_page']; $i++)
                    @if($i == $pagination['current_page'])
                        <span class="px-3 py-2 text-sm font-medium text-white bg-emerald-600 border border-emerald-600 rounded-md">
                            {{ $i }}
                        </span>
                    @else
                        <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}" 
                            class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            {{ $i }}
                        </a>
                    @endif
                @endfor

                @if($pagination['current_page'] < $pagination['last_page'])
                    <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] + 1]) }}" 
                        class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        {{ __('Next', 'fmr') }}
                    </a>
                @endif
            </nav>
        </div>
    @endif
@endsection
