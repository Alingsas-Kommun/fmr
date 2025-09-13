@extends('layouts.app')

@section('content')
    <div class="bg-white dark:bg-gray-100 rounded-lg shadow overflow-hidden p-6 mt-8">
        <h1 class="text-2xl font-bold mb-6">{!! __('Assignment Details', 'fmr') !!}</h1>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <h2 class="text-lg font-semibold mb-4">{!! __('Board', 'fmr') !!}</h2>
                
                @if($assignment->board)
                    <a href="{{ get_permalink($assignment->board->ID) }}" class="text-emerald-700 hover:text-emerald-800">{{ $assignment->board->post_title }}</a>
                @endif
            </div>

            <div>
                <h2 class="text-lg font-semibold mb-4">{!! __('Decision Authority', 'fmr') !!}</h2>
                
                @if($assignment->decisionAuthority)
                    <a href="{{ route('decision-authorities.show', $assignment->decisionAuthority) }}" class="text-emerald-700 hover:text-emerald-800">{{ $assignment->decisionAuthority->title }}</a>
                @endif
            </div>

            <div>
                <h2 class="text-lg font-semibold mb-4">{!! __('Person', 'fmr') !!}</h2>
                
                @if($assignment->person)
                    <a href="{{ get_permalink($assignment->person->ID) }}" class="text-emerald-700 hover:text-emerald-800">{{ $assignment->person->post_title }}</a>
                @endif
            </div>

            <div>
                <h2 class="text-lg font-semibold mb-4">{!! __('Role', 'fmr') !!}</h2>
                <p class="text-gray-700">{{ $assignment->role }}</p>
            </div>

            <div>
                <h2 class="text-lg font-semibold mb-4">{!! __('Period', 'fmr') !!}</h2>
                <p class="text-gray-700">
                    {{ $assignment->period_start->format('Y-m-d') }} - {{ $assignment->period_end->format('Y-m-d') }}
                </p>
            </div>
        </div>
    </div>
@endsection
