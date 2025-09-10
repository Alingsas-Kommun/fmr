@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-15">
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
@endsection