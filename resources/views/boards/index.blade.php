@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold">{!! __('Boards', 'fmr') !!}</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($boards as $board)
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                    <a href="{{ route('boards.show', $board) }}" class="block p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">
                            {{ $board->post_title }}
                        </h2>
                        
                        @if($board->post_excerpt)
                            <p class="text-gray-600 text-sm line-clamp-3">
                                {{ $board->post_excerpt }}
                            </p>
                        @endif

                        <div class="mt-4 inline-flex items-center text-sm text-emerald-600 font-medium">
                            {!! __('View board', 'fmr') !!}
                            <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endsection
