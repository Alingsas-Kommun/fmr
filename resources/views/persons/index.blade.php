@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Persons</h1>
            
            @if($pagination['total'] > 0)
                <p class="text-gray-600">
                    Showing {{ $pagination['from'] }} to {{ $pagination['to'] }} of {{ $pagination['total'] }} persons
                </p>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($persons as $person)
                <div class="relative bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-6 flex items-center gap-6">
                        <div class="flex-shrink-0">
                            @if($person->thumbnail())
                                <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-100">
                                    {!! $person->thumbnail() !!}
                                </div>
                            @else
                                <div class="w-24 h-24 rounded-full bg-emerald-100 flex items-center justify-center">
                                    <span class="text-2xl text-emerald-700">{{ substr($person->post_title, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="flex-grow min-w-0">
                            <h2 class="text-xl font-semibold mb-1 truncate">{{ $person->post_title }}</h2>
                            
                            @php
                                $position = $person->getMeta('position');
                                $email = $person->getMeta('email');
                                $phone = $person->getMeta('phone');
                            @endphp

                            @if($position)
                                <p class="text-gray-600 mb-3">{{ $position }}</p>
                            @endif

                            <div class="space-y-1">
                                @if($email)
                                    <p class="text-gray-600 text-sm flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <a href="mailto:{{ $email }}" class="text-green-700 hover:text-green-800 truncate">
                                            {{ $email }}
                                        </a>
                                    </p>
                                @endif

                                @if($phone)
                                    <p class="text-gray-600 text-sm flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        <a href="tel:{{ $phone }}" class="text-green-700 hover:text-green-800">
                                            {{ $phone }}
                                        </a>
                                    </p>
                                @endif
                            </div>
                        </div>

                        <a href="{{ route('persons.show', $person) }}" class="after:absolute after:inset-0"></a>
                    </div>
                </div>
            @endforeach
        </div>

        @if($pagination['last_page'] > 1)
            <div class="mt-8 flex justify-center items-center gap-4">
                @if($pagination['has_previous_pages'])
                    <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] - 1]) }}"
                       class="px-4 py-2 bg-white border border-gray-300 rounded-md text-green hover:bg-gray-50 transition-colors">
                        &larr; Previous
                    </a>
                @endif

                <div class="flex items-center gap-2">
                    @php
                        $range = 2; // Show 2 pages before and after current page
                        $start = max(1, $pagination['current_page'] - $range);
                        $end = min($pagination['last_page'], $pagination['current_page'] + $range);
                    @endphp

                    @if($start > 1)
                        <a href="{{ request()->fullUrlWithQuery(['page' => 1]) }}"
                           class="px-3 py-1 bg-white border border-gray-300 rounded-md hover:bg-gray-50">1</a>
                        @if($start > 2)
                            <span class="px-2">...</span>
                        @endif
                    @endif

                    @for($i = $start; $i <= $end; $i++)
                        <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}"
                           class="px-3 py-1 {{ $i === $pagination['current_page'] 
                                ? 'bg-green text-white' 
                                : 'bg-white border border-gray-300 hover:bg-gray-50' }} rounded-md">
                            {{ $i }}
                        </a>
                    @endfor

                    @if($end < $pagination['last_page'])
                        @if($end < $pagination['last_page'] - 1)
                            <span class="px-2">...</span>
                        @endif
                        <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['last_page']]) }}"
                           class="px-3 py-1 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            {{ $pagination['last_page'] }}
                        </a>
                    @endif
                </div>

                @if($pagination['has_more_pages'])
                    <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] + 1]) }}"
                       class="px-4 py-2 bg-white border border-gray-300 rounded-md text-green hover:bg-gray-50 transition-colors">
                        Next &rarr;
                    </a>
                @endif
            </div>
        @endif
    </div>
@endsection