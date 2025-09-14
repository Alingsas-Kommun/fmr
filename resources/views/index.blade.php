@extends('layouts.app')

@section('content')
    @noposts
        <x-alert type="warning">
            {!! __('Sorry, no results were found.', 'fmr') !!}
        </x-alert>
    @endnoposts

    @posts
        @includeFirst(['partials.post-types.content-' . get_post_type(), 'partials.post-types.content'])
    @endposts
@endsection
