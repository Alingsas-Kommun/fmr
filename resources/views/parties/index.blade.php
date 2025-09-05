@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <div class="mb-12 text-center">
            <h1 class="text-4xl font-bold text-gray-900">{!! __('Parties', 'fmr') !!}</h1>
        </div>

        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($parties as $party)
                    <div class="flex flex-col items-center justify-center w-full">
                        <a href="{{ route('parties.show', $party) }}" class="group w-full text-center">
                            @if($party->thumbnail())
                                <div class="flex items-center justify-center h-32 mb-4">
                                    <div class="flex items-center justify-center">
                                        {!! $party->thumbnail() !!}
                                    </div>
                                </div>
                            @endif

                            <h2 class="text-base font-medium text-gray-900 group-hover:text-emerald-700 transition-colors">
                                {{ $party->post_title }}
                            </h2>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
