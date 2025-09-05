@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold mb-4">{{ $board->post_title }}</h1>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold">{!! __('Current Assignments', 'fmr') !!}</h2>
            </div>

            @if($assignments->isNotEmpty())
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {!! __('Person', 'fmr') !!}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {!! __('Role', 'fmr') !!}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {!! __('Party', 'fmr') !!}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {!! __('Period', 'fmr') !!}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($assignments as $assignment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($assignment->person)
                                        <a href="{{ route('persons.show', $assignment->person) }}" 
                                           class="text-green-700 hover:text-green-800">
                                            {{ $assignment->person->post_title }}
                                        </a>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $assignment->role }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($assignment->person)
                                       
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $assignment->period_start->format('Y-m-d') }} - {{ $assignment->period_end->format('Y-m-d') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="px-6 py-4 text-gray-500">
                    {!! __('No active assignments found.', 'fmr') !!}
                </div>
            @endif
        </div>
    </div>
@endsection
