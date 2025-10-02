@extends('layouts.app')

@section('content')
    @if($setting('show_search', true))
        <div class="bg-primary-50 rounded-xl mt-3">
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

    @if($parties->isNotEmpty())
        <div class="pt-15 pb-6">
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-2 md:grid-cols-5 gap-8">
                    @foreach($parties as $party)
                        <a href="{{ $party->url }}" class="group flex flex-col items-center text-center">
                            @if($party->image())
                                <div class="flex items-center justify-center mb-3">
                                    {!! $party->image('medium', 'h-16 w-auto') !!}
                                </div>
                            @else
                                <div class="flex items-center justify-center mb-3">
                                    <x-heroicon-o-user-group class="h-16 w-16 text-gray-400" />
                                </div>
                            @endif
                            
                            <h3 class="text-sm font-semibold text-gray-900 group-hover:text-primary-700 transition-colors duration-200 mb-1">
                                {!! $party->name !!}
                            </h3>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if($groupLeaders->isNotEmpty())
        <div class="py-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">{!! __('Group Leaders', 'fmr') !!}</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($groupLeaders as $leader)
                    <a href="{!! $leader->url !!}" class="group bg-primary-50 rounded-lg duration-200 p-4">
                        <div class="flex items-center space-x-4">
                            @if($leader->image())
                                <div class="flex-shrink-0">
                                    {!! $leader->image('medium', 'w-16 h-16 rounded-full') !!}
                                </div>
                            @else
                                <div class="w-16 h-16 md:bg-white rounded-full flex items-center justify-center flex-shrink-0">
                                    <x-heroicon-o-user class="h-8 w-8 text-primary-600" />
                                </div>
                            @endif

                            <div class="flex-1 min-w-0 space-y-1">
                                <h3 class="text-md font-semibold text-gray-900 group-hover:text-primary-700 transition-colors duration-200">
                                    @if($leader->meta->firstname && $leader->meta->lastname)
                                        {!! $leader->meta->firstname !!} {!! $leader->meta->lastname !!}
                                    @else
                                        {!! $leader->name !!}
                                    @endif
                                </h3>

                                @if($leader->party)
                                    <span class="flex items-center space-x-1 text-sm">
                                        @if($leader->party->image())
                                            {!! $leader->party->image('medium', 'w-4 h-4 flex-shrink-0') !!}
                                        @else
                                            <x-heroicon-o-user-group class="h-5 w-5 text-primary-600 flex-shrink-0" />
                                        @endif

                                        <span>{{ $leader->party->name }}</span>
                                    </span>
                                @endif
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

    @if($groupedAuthorities->isNotEmpty())
        <div class="py-8">            
            @foreach($groupedAuthorities as $typeName => $authorities)
                <div class="mb-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">{!! $typeName !!}</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($authorities as $authority)
                            <a href="{{ route('decision-authorities.show', $authority->id) }}" class="group bg-primary-50 rounded-lg duration-200 p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 min-w-0 space-y-1">
                                        @if($authority->board)
                                            @set($board, $authority->board->format())
                                            
                                            <span class="inline-flex items-center text-xs font-medium text-primary-600 space-x-1">
                                                <x-heroicon-o-building-office-2 class="h-3 w-3 text-primary-600 flex-shrink-0" />
                                                <span>{!! $board->name !!}</span>
                                            </span>
                                        @endif

                                        <h4 class="text-md font-semibold text-gray-900 group-hover:text-primary-700 transition-colors duration-200 mb-2">
                                            {!! $authority->title !!}
                                        </h4>
                                    </div>

                                    <div class="flex-shrink-0 ml-4">
                                        <x-heroicon-o-arrow-right class="h-5 w-5 text-gray-400 group-hover:text-primary-700 transition-colors duration-200" />
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection