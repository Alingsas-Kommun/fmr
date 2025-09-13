@extends('layouts.app')

@section('content')
    @while(have_posts()) @php(the_post())
        <div class="mt-8">
            @includeFirst(['partials.content-single-' . get_post_type(), 'partials.content-single'])
        </div>
    @endwhile
@endsection
