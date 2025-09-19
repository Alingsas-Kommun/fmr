<div class="fields-group">
    <div class="fields-row">
        <div class="fields-field-row">
            <div class="fields-field-col" style="--cols: 6">
                <div class="fields-field">
                    <label for="person_id">{{ __('Person', 'fmr') }}</label>
                    <select name="person_id" id="person_id" class="widefat" required>
                        <option value="">{{ __('Select Person', 'fmr') }}</option>
                        
                        @foreach($persons as $person)
                            <option value="{{ $person->ID }}" {{ $getFieldValue('person_id') == $person->ID ? 'selected' : '' }}>
                                {{ $person->post_title }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="fields-field-col" style="--cols: 6">
                <div class="fields-field">
                    <label for="role_term_id">{{ __('Role', 'fmr') }}</label>
                    <select name="role_term_id" id="role_term_id" class="widefat" required>
                        <option value="">{{ __('Select Role', 'fmr') }}</option>
                        
                        @foreach($roles as $term)
                            <option value="{{ $term->term_id }}" {{ $getFieldValue('role_term_id') == $term->term_id ? 'selected' : '' }}>
                                {{ $term->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="fields-row">
        <div class="fields-field-row">
            <div class="fields-field-col" style="--cols: 12">
                <div class="fields-field">
                    <label for="decision_authority_id">{{ __('Decision Authority', 'fmr') }}</label>
                    <select name="decision_authority_id" id="decision_authority_id" class="widefat" required>
                        <option value="">{{ __('Select Decision Authority', 'fmr') }}</option>
                        
                        @php
                            $groupedAuthorities = $decisionAuthorities->groupBy(function($authority) {
                                return $authority->board->post_title;
                            });
                        @endphp

                        @foreach($groupedAuthorities as $boardTitle => $authorities)
                            <optgroup label="{{ $boardTitle }}">
                                @foreach($authorities as $authority)
                                    <option value="{{ $authority->id }}" {{ $getFieldValue('decision_authority_id') == $authority->id ? 'selected' : '' }}>
                                        {{ $authority->title }} ({{ date('Y-m-d', strtotime($authority->start_date)) }} - {{ date('Y-m-d', strtotime($authority->end_date)) }})
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="fields-row">
        <div class="fields-field-row">
            <div class="fields-field-col" style="--cols: 6">
                <div class="fields-field">
                    <label for="period_start">{{ __('Start Date', 'fmr') }}</label>
                    <input type="date" name="period_start" id="period_start" value="{{ $getFieldValue('period_start') ? date('Y-m-d', strtotime($getFieldValue('period_start'))) : '' }}"  class="widefat" required>
                </div>
            </div>
            <div class="fields-field-col" style="--cols: 6">
                <div class="fields-field">
                    <label for="period_end">{{ __('End Date', 'fmr') }}</label>
                    <input type="date" name="period_end" id="period_end" value="{{ $getFieldValue('period_end') ? date('Y-m-d', strtotime($getFieldValue('period_end'))) : '' }}" class="widefat" required>
                </div>
            </div>
        </div>
    </div>
</div>
