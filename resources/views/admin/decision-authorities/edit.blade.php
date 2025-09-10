<div class="wrap">
    <h1 class="wp-heading-inline">{{ $decisionAuthority->exists ? __('Edit Decision Authority', 'fmr') : __('Add New Decision Authority', 'fmr') }}</h1>
    <a href="{{ admin_url('admin.php?page=decision_authority_edit') }}" class="page-title-action">{{ __('Add new decision authority', 'fmr') }}</a>

    <hr class="wp-header-end">

    <form action="{{ admin_url('admin-post.php') }}" method="post">
        {!! wp_nonce_field('save_decision_authority', '_wpnonce', true, false) !!}
        <input type="hidden" name="action" value="save_decision_authority">
        
        @if($decisionAuthority->exists)
            <input type="hidden" name="id" value="{{ $decisionAuthority->id }}">
        @endif

        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="board_id">{{ __('Board', 'fmr') }}</label>
                    </th>
                    <td>
                        <select name="board_id" id="board_id" class="regular-text" required>
                            <option value="">{{ __('Select Board', 'fmr') }}</option>
                            @foreach($boards as $board)
                                <option value="{{ $board->ID }}" {{ old('board_id', $decisionAuthority->board_id) == $board->ID ? 'selected' : '' }}>
                                    {{ $board->post_title }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="title">{{ __('Title', 'fmr') }}</label>
                    </th>
                    <td>
                        <input type="text" 
                               name="title" 
                               id="title" 
                               value="{{ old('title', $decisionAuthority->title) }}" 
                               class="regular-text"
                               required>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="type">{{ __('Type', 'fmr') }}</label>
                    </th>
                    <td>
                        <select name="type" id="type" class="regular-text" required>
                            <option value="">{{ __('Select Type', 'fmr') }}</option>
                            @foreach(['Nämnd', 'Styrelse', 'Utskott', 'Beredning', 'Råd'] as $type)
                                <option value="{{ $type }}" {{ old('type', $decisionAuthority->type) == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="start_date">{{ __('Start Date', 'fmr') }}</label>
                    </th>
                    <td>
                        <input type="date" 
                               name="start_date" 
                               id="start_date" 
                               value="{{ old('start_date', $decisionAuthority->start_date ? date('Y-m-d', strtotime($decisionAuthority->start_date)) : '') }}" 
                               class="regular-text"
                               required>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="end_date">{{ __('End Date', 'fmr') }}</label>
                    </th>
                    <td>
                        <input type="date" 
                               name="end_date" 
                               id="end_date" 
                               value="{{ old('end_date', $decisionAuthority->end_date ? date('Y-m-d', strtotime($decisionAuthority->end_date)) : '') }}" 
                               class="regular-text"
                               required>
                    </td>
                </tr>
            </tbody>
        </table>

        <p class="submit">
            <button type="submit" class="button button-primary">
                {{ $decisionAuthority->exists ? __('Update Decision Authority', 'fmr') : __('Create Decision Authority', 'fmr') }}
            </button>
        </p>
    </form>
</div>
