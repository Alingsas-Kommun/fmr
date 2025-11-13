<div class="wrap anniversary-page">
    <h1>{{ __('Anniversaries', 'fmr') }}</h1>

    <div class="anniversary-filters">
        <form method="GET" action="">
            <input type="hidden" name="page" value="anniversaries">
            
            <div class="filter-row">
                <div class="filter-controls">
                    <select id="min-years" name="min-years">
                        <option value="" {{ $minYears === null ? 'selected' : '' }}>{{ __('Choose min years', 'fmr') }}</option>

                        @for($i = 0; $i <= 50; $i += 1)
                            <option value="{{ $i }}" {{ $minYears == $i && $minYears !== null ? 'selected' : '' }}>
                                {!! $i == 1 ? sprintf(__("%d year", "fmr"), $i) : sprintf(__("%d years", "fmr"), $i) !!}
                            </option>
                        @endfor
                    </select>
                    
                    <select id="max-years" name="max-years">
                        <option value="">{{ __('Choose max years', 'fmr') }}</option>

                        @for($i = 1; $i <= 50; $i += 1)
                            <option value="{{ $i }}" {{ $maxYears == $i ? 'selected' : '' }}>
                                {!! $i == 1 ? sprintf(__("%d year", "fmr"), $i) : sprintf(__("%d years", "fmr"), $i) !!}
                            </option>
                        @endfor
                    </select>
                    
                    <select id="board" name="board">
                        <option value="">{{ __('Choose board', 'fmr') }}</option>
                        @foreach($boards as $board)
                            <option value="{{ $board->ID }}" {{ $boardId == $board->ID ? 'selected' : '' }}>
                                {{ $board->post_title }}
                            </option>
                        @endforeach
                    </select>
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
                        @php
                            $exportParams = [];
                            if ($minYears !== null) {
                                $exportParams[] = 'min-years=' . $minYears;
                            }
                            if ($maxYears !== null) {
                                $exportParams[] = 'max-years=' . $maxYears;
                            }
                            if ($boardId !== null) {
                                $exportParams[] = 'board=' . $boardId;
                            }
                            $exportQueryString = !empty($exportParams) ? '&' . implode('&', $exportParams) : '';
                        @endphp
                        <a href="{{ admin_url('admin.php?page=anniversaries&export=excel' . $exportQueryString) }}" class="button button-secondary">
                            📊 {{ __('Export Excel', 'fmr') }}
                        </a>
                        <a href="{{ admin_url('admin.php?page=anniversaries&export=csv' . $exportQueryString) }}" class="button button-secondary">
                            📄 {{ __('Export CSV', 'fmr') }}
                        </a>
                    </div>
                </div>
            @endif
            
            @if($results->isEmpty() || $results->count() == 0)
                <div class="notice notice-info">
                    <p>{{ __('No persons found matching the specified criteria.', 'fmr') }}</p>
                </div>
            @else
                @foreach($results as $result)
                    <div class="person-result">
                        <h4>{{ $result['person']->post_title }}</h4>
                        
                        <div class="assignments-list">
                            <table class="widefat">
                                <thead>
                                    <tr>
                                        <th>{{ __('Position', 'fmr') }}</th>
                                        <th>{{ __('Start Date', 'fmr') }}</th>
                                        <th>{{ __('End Date', 'fmr') }}</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($result['assignments'] as $assignment)
                                        <tr>
                                            <td>{{ $assignment->roleTerm->name ?? __('Unknown Role', 'fmr') }}</td>
                                            <td>{{ $assignment->period_start->format('Y-m-d H:i:s') }}</td>
                                            <td>{{ $assignment->period_end->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="service-summary">
                            <strong>{{ __('Result:', 'fmr') }}</strong> {{ $result['service_display'] }}
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