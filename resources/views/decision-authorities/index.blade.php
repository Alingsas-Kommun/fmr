@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">{{ __('Decision Authorities', 'fmr') }}</h1>

        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <form action="{{ url()->current() }}" method="get" class="flex gap-4 items-end">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Type', 'fmr') }}</label>
                    
                    <select name="type" id="type" class="form-select" onchange="this.form.submit()">
                        <option value="">{{ __('All Types', 'fmr') }}</option>

                        @foreach(['Nämnd', 'Styrelse', 'Utskott', 'Beredning', 'Råd'] as $type)
                            <option value="{{ $type }}" {{ $filters['type'] === $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                @if($filters['type'])
                    <a href="{{ url()->current() }}" class="text-emerald-600 hover:text-emerald-800">
                        {{ __('Clear filters', 'fmr') }}
                    </a>
                @endif
            </form>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Title', 'fmr') }}
                        </th>
                        
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Board', 'fmr') }}
                        </th>

                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Type', 'fmr') }}
                        </th>

                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Period', 'fmr') }}
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($decisionAuthorities as $authority)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <a href="{{ route('decision-authorities.show', $authority) }}" class="text-emerald-700 hover:text-emerald-800">
                                    {{ $authority->title }}
                                </a>
                            </td>

                            <td class="px-6 py-4">
                                @if($authority->board)
                                    <a href="{{ get_permalink($authority->board->ID) }}" class="text-emerald-700 hover:text-emerald-800">
                                        {{ $authority->board->post_title }}
                                    </a>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $authority->type }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    {{ $authority->start_date->format('j M Y') }} - {{ $authority->end_date->format('j M Y') }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                {{ __('No decision authorities found.', 'fmr') }}
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
    </div>
@endsection
