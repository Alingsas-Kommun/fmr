@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="mb-6">
                <h1 class="text-3xl font-bold mb-4">{{ $party->post_title }}</h1>
            </div>

            @if($party->thumbnail())
                <div class="mb-6">
                    {!! $party->thumbnail() !!}
                </div>
            @endif
        </div>
    </div>
@endsection
