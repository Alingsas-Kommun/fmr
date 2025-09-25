@extends('layouts.app')

@section('content')
    @if($setting('show_search', true))
        <div class="bg-gray-50 dark:bg-gray-100 rounded-xl mt-3">
            <div class="mx-auto max-w-3xl py-15 px-4">
                <div class="text-center">
                    <h1 class="text-3xl font-bold tracking-tight text-primary-600 sm:text-4xl text-balance">
                        {!! __('Find your politician in Alingsås', 'fmr') !!}
                    </h1>

                    <p class="mt-3 text-lg leading-8 text-gray-800">
                        {!! __('Search through assignments, parties and politicians in the municipality of Alingsås.', 'fmr') !!}
                    </p>
                </div>
                
                <div class="mt-7">
                    <livewire:search />
                </div>
            </div>
        </div>
    @endif

    <div class="py-15">
        <div class="max-w-6xl mx-auto">
            <div class="flex flex-wrap justify-center items-center gap-12">
                @foreach($parties as $party)
                    <a href="{{ get_permalink($party->ID) }}" class="group flex flex-col items-center">
                        @if($party->thumbnail())
                            <div class="flex items-center justify-center">
                                {!! $party->thumbnail('h-17 w-auto') !!}
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    @if($groupLeaders->isNotEmpty())
        <div class="py-8">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">{!! __('Group Leaders', 'fmr') !!}</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($groupLeaders as $leader)
                    <a href="{{ get_permalink($leader->ID) }}" class="group bg-gray-50 dark:bg-gray-100 rounded-lg duration-200 p-4">
                        <div class="flex items-center space-x-4">
                            @if($leader->thumbnail())
                                <div class="flex-shrink-0">
                                    <div class="w-16 h-16 rounded-full overflow-hidden">
                                        {!! $leader->thumbnail() !!}
                                    </div>
                                </div>
                            @else
                                <div class="w-16 h-16 md:bg-white rounded-full flex items-center justify-center flex-shrink-0">
                                    <x-heroicon-o-user class="h-8 w-8 text-primary-600" />
                                </div>
                            @endif

                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-primary-700 transition-colors duration-200">
                                    @if($leader->getMeta('person_firstname') && $leader->getMeta('person_lastname'))
                                        {{ $leader->getMeta('person_firstname') }} {{ $leader->getMeta('person_lastname') }}
                                    @else
                                        {{ $leader->post_title }}
                                    @endif
                                </h3>
                            </div>

                            <div class="flex-shrink-0">
                                <x-heroicon-o-arrow-right class="h-5 w-5 text-gray-400 group-hover:text-primary-700 transition-colors duration-200" />
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    @if($boards->isNotEmpty())
        <div class="py-8">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">{!! __('Boards & Committees', 'fmr') !!}</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($boards as $board)
                    <a href="{{ get_permalink($board->ID) }}" class="group bg-gray-50 dark:bg-gray-100 rounded-lg duration-200 p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-primary-700 transition-colors duration-200 mb-2">
                                    {{ $board->post_title }}
                                </h3>

                                @if($board->getMeta('board_category'))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-600">
                                        {{ $board->getMeta('board_category') }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex-shrink-0 ml-4">
                                <x-heroicon-o-arrow-right class="h-5 w-5 text-gray-400 group-hover:text-primary-700 transition-colors duration-200" />
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
@endsection