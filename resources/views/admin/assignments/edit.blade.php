<div class="wrap">
    <h1 class="wp-heading-inline">{{ $assignment->exists ? __('Edit Assignment', 'fmr') : __('Add New Assignment', 'fmr') }}</h1>
    <a href="{{ admin_url('admin.php?page=assignment_edit') }}" class="page-title-action">{{ __('Add new assignment', 'fmr') }}</a>

    <hr class="wp-header-end">

    <form action="{{ admin_url('admin-post.php') }}" method="post">
        {!! wp_nonce_field('save_assignment', '_wpnonce', true, false) !!}
        <input type="hidden" name="action" value="save_assignment">
        
        @if($assignment->exists)
            <input type="hidden" name="id" value="{{ $assignment->id }}">
        @endif

        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="person_id">{{ __('Person', 'fmr') }}</label>
                    </th>
                    <td>
                        <select name="person_id" id="person_id" class="regular-text">
                            <option value="">{{ __('Select Person', 'fmr') }}</option>
                            @foreach($persons as $person)
                                <option value="{{ $person->ID }}" {{ old('person_id', $assignment->person_id) == $person->ID ? 'selected' : '' }}>
                                    {{ $person->post_title }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="decision_authority_id">{{ __('Decision Authority', 'fmr') }}</label>
                    </th>
                    <td>
                        <select name="decision_authority_id" id="decision_authority_id" class="regular-text">
                            <option value="">{{ __('Select Decision Authority', 'fmr') }}</option>
                            @php
                                $groupedAuthorities = $decisionAuthorities->groupBy(function($authority) {
                                    return $authority->board->post_title;
                                });
                            @endphp

                            @foreach($groupedAuthorities as $boardTitle => $authorities)
                                <optgroup label="{{ $boardTitle }}">
                                    @foreach($authorities as $authority)
                                        <option value="{{ $authority->id }}" {{ old('decision_authority_id', $assignment->decision_authority_id) == $authority->id ? 'selected' : '' }}>
                                            {{ $authority->title }} ({{ date('Y-m-d', strtotime($authority->start_date)) }} - {{ date('Y-m-d', strtotime($authority->end_date)) }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="role">{{ __('Role', 'fmr') }}</label>
                    </th>
                    <td>
                        <input type="text" 
                               name="role" 
                               id="role" 
                               value="{{ old('role', $assignment->role) }}" 
                               class="regular-text">
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="period_start">{{ __('Start Date', 'fmr') }}</label>
                    </th>
                    <td>
                        <input type="date" 
                               name="period_start" 
                               id="period_start" 
                               value="{{ old('period_start', $assignment->period_start ? date('Y-m-d', strtotime($assignment->period_start)) : '') }}" 
                               class="regular-text">
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="period_end">{{ __('End Date', 'fmr') }}</label>
                    </th>
                    <td>
                        <input type="date" 
                               name="period_end" 
                               id="period_end" 
                               value="{{ old('period_end', $assignment->period_end ? date('Y-m-d', strtotime($assignment->period_end)) : '') }}" 
                               class="regular-text">
                    </td>
                </tr>
            </tbody>
        </table>

        <p class="submit">
            <button type="submit" class="button button-primary">
                {{ $assignment->exists ? __('Update Assignment', 'fmr') : __('Create Assignment', 'fmr') }}
            </button>
        </p>
    </form>
</div>
