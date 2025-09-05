<div>
    <h2>
        <a href="{{ get_permalink() }}">
            {!! $title !!}
        </a>
    </h2>

    @php(the_excerpt())
</div>
