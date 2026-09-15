<div class="wrap anniversary-page">
    <h1>{{ __('Anniversaries', 'fmr') }}</h1>

    <div class="anniversary-filters">
        <form method="GET" action="">
            <input type="hidden" name="page" value="anniversaries">

            <div class="filter-row">
                <div class="filter-controls">
                    <select id="min-years" name="min-years">
                        <option value="">{{ __('Choose min years', 'fmr') }}</option>

                        @for($i = 0; $i <= 50; $i += 1)
                            <option value="{{ $i }}" {{ selected($filters['min_years'] ?? '', $i, false) }}>
                                {!! $i == 1 ? sprintf(__("%d year", "fmr"), $i) : sprintf(__("%d years", "fmr"), $i) !!}
                            </option>
                        @endfor
                    </select>

                    <select id="max-years" name="max-years">
                        <option value="">{{ __('Choose max years', 'fmr') }}</option>

                        @for($i = 1; $i <= 50; $i += 1)
                            <option value="{{ $i }}" {{ selected($filters['max_years'] ?? '', $i, false) }}>
                                {!! $i == 1 ? sprintf(__("%d year", "fmr"), $i) : sprintf(__("%d years", "fmr"), $i) !!}
                            </option>
                        @endfor
                    </select>

                    <select id="board" name="board">
                        <option value="">{{ __('Choose board', 'fmr') }}</option>
                        @foreach($boards as $board)
                            <option value="{{ $board->ID }}" {{ selected($filters['board_id'] ?? '', $board->ID, false) }}>
                                {{ $board->post_title }}
                            </option>
                        @endforeach
                    </select>

                    <x-admin.multi-select
                        id="roles"
                        name="roles[]"
                        :options="$roles->mapWithKeys(fn ($role) => [$role->term_id => $role->name])->all()"
                        :selected="$filters['role_ids'] ?? []"
                        placeholder="{{ __('Choose roles', 'fmr') }}"
                    />
                </div>

                <div class="filter-actions">
                    <input type="submit" class="button button-primary" value="{{ __('Apply filters', 'fmr') }}">

                    @if($hasFilters)
                        <a href="?page=anniversaries" class="button">{{ __('Clear filters', 'fmr') }}</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    @if($hasFilters)
        <div class="anniversary-results">
            @if($results->count() > 0)
                <div class="results-header">
                    <h3>{{ __('Results', 'fmr') }} ({!! sprintf(_n('%d person found', '%d persons found', $results->count(), 'fmr'), $results->count()) !!})</h3>

                    <div class="export-buttons">
                        <a href="{!! esc_url(add_query_arg(['export' => 'excel'], $_SERVER['REQUEST_URI'])) !!}" class="button button-secondary">
                            📊 {{ __('Export Excel', 'fmr') }}
                        </a>

                        <a href="{!! esc_url(add_query_arg(['export' => 'csv'], $_SERVER['REQUEST_URI'])) !!}" class="button button-secondary">
                            📄 {{ __('Export CSV', 'fmr') }}
                        </a>
                    </div>
                </div>
            @endif

            @if($results->isEmpty())
                <div class="notice notice-info">
                    <p>{{ __('No persons found matching the specified criteria.', 'fmr') }}</p>
                </div>
            @else
                @foreach($results as $result)
                    <div class="person-result">
                        <h4>
                            {{ $result['full_name'] }}
                            @if($result['party_name'])
                                <span>({{ $result['party_name'] }})</span>
                            @endif
                        </h4>

                        <div class="assignments-list">
                            <table class="widefat">
                                <thead>
                                    <tr>
                                        <th>{{ __('Position', 'fmr') }}</th>
                                        <th>{{ __('Board', 'fmr') }}</th>
                                        <th>{{ __('Start Date', 'fmr') }}</th>
                                        <th>{{ __('End Date', 'fmr') }}</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($result['assignments'] as $assignment)
                                        <tr>
                                            <td>{{ $assignment->roleTerm->name ?? __('Unknown Role', 'fmr') }}</td>
                                            <td>{{ $assignment->board?->post_title ?? '' }}</td>
                                            <td>{{ $assignment->period_start->format('Y-m-d H:i:s') }}</td>
                                            <td>{{ $assignment->period_end->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="service-summary">
                            <strong>{{ __('Total time (overlapping periods counted once)', 'fmr') }}:</strong> {{ $result['service_display'] }}
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    @else
        <div class="notice notice-warning">
            <p>{{ __('Use the filters above to find persons by their years of service.', 'fmr') }}</p>
        </div>
    @endif
</div>
