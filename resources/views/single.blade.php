@extends('layouts.app')

@section('content')
    @posts
        <div class="mt-3">
            @includeFirst(['partials.post-types.content-single-' . get_post_type(), 'partials.post-types.content-single'])
        </div>
    @endposts
@endsection
