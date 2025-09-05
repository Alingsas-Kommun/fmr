@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">{!! __('Assignments', 'fmr') !!}</h1>
            
            <div class="flex gap-4">
                <form method="GET" class="flex gap-4">
                    <select name="role" class="form-select">
                        <option value="">{!! __('All roles', 'fmr') !!}</option>
                        <option value="Ordförande" {{ $filters['role'] === 'Ordförande' ? 'selected' : '' }}>{!! __('Chairman', 'fmr') !!}</option>
                        <option value="Vice ordförande" {{ $filters['role'] === 'Vice ordförande' ? 'selected' : '' }}>{!! __('Vice Chairman', 'fmr') !!}</option>
                        <option value="Ledamot" {{ $filters['role'] === 'Ledamot' ? 'selected' : '' }}>{!! __('Member', 'fmr') !!}</option>
                        <option value="Ersättare" {{ $filters['role'] === 'Ersättare' ? 'selected' : '' }}>{!! __('Deputy', 'fmr') !!}</option>
                    </select>

                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="active" value="1" {{ $filters['active'] ? 'checked' : '' }} class="form-checkbox">
                        <span>{!! __('Active only', 'fmr') !!}</span>
                    </label>

                    <button type="submit" class="bg-green-800 text-white px-4 py-2 rounded hover:bg-green-600 transition-colors">{!! __('Filter', 'fmr') !!}</button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! __('Board', 'fmr') !!}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! __('Person', 'fmr') !!}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! __('Role', 'fmr') !!}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! __('Period', 'fmr') !!}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! __('Actions', 'fmr') !!}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($assignments as $assignment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($assignment->board)
                                    {{ $assignment->board->post_title }}
                                @endif
                            </td>
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
                                {{ $assignment->period_start->format('Y-m-d') }} - {{ $assignment->period_end->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('assignments.show', $assignment) }}" class="text-green-700 hover:text-green-800">{!! __('View', 'fmr') !!}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($pagination['last_page'] > 1)
            <div class="mt-4 flex justify-center gap-2">
                @if($pagination['current_page'] > 1)
                    <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] - 1]) }}" 
                       class="px-4 py-2 bg-white border rounded hover:bg-gray-50">&larr; {!! __('Previous', 'fmr') !!}</a>
                @endif

                <span class="px-4 py-2 bg-white border rounded">
                    {!! __('Page', 'fmr') !!} {{ $pagination['current_page'] }} {!! __('of', 'fmr') !!} {{ $pagination['last_page'] }}
                </span>

                @if($pagination['current_page'] < $pagination['last_page'])
                    <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] + 1]) }}" 
                       class="px-4 py-2 bg-white border rounded hover:bg-gray-50">{!! __('Next', 'fmr') !!} &rarr;</a>
                @endif
            </div>
        @endif
    </div>
@endsection
