<div class="wrap">
    <h1 class="wp-heading-inline">{{ __('Assignments', 'fmr') }}</h1>
    <a href="{{ admin_url('admin.php?page=assignment_edit') }}" class="page-title-action">{{ __('Add new assignment', 'fmr') }}</a>
    <hr class="wp-header-end">
    
    <form action="" method="GET">
        @php
            $list->prepare_items();
            $list->views();
            $list->search_box(__('Search Assignments', 'fmr'), 'assignment');
        @endphp

        <div class="clear"></div>

        <div class="filter-bar">            
            <div class="filter-row">
                <div class="filter-bar-group">
                    <label for="role_filter">{{ __('Role', 'fmr') }}</label>
                    <select name="role_filter" id="role_filter">
                        <option value="">{{ __('All roles', 'fmr') }}</option>
                        
                        @foreach($filter_data['roles'] as $role)
                            <option value="{{ $role->term_id }}" {{ selected($_REQUEST['role_filter'] ?? '', $role->term_id) }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-bar-group">
                    <label for="board_filter">{{ __('Board', 'fmr') }}</label>
                    <select name="board_filter" id="board_filter">
                        <option value="">{{ __('All boards', 'fmr') }}</option>

                        @foreach($filter_data['boards'] as $board)
                            <option value="{{ $board->ID }}" {{ selected($_REQUEST['board_filter'] ?? '', $board->ID) }}>
                                {{ $board->post_title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-bar-group">
                    <label for="person_filter">{{ __('Persons', 'fmr') }}</label>
                    <select name="person_filter" id="person_filter">
                        <option value="">{{ __('All persons', 'fmr') }}</option>

                        @foreach($filter_data['persons'] as $person)
                            <option value="{{ $person->ID }}" {{ selected($_REQUEST['person_filter'] ?? '', $person->ID) }}>
                                {{ $person->post_title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-bar-group">
                    <label for="period_start">{{ __('Start Date', 'fmr') }}</label>
                    <input type="date" name="period_start" id="period_start" value="{{ $_REQUEST['period_start'] ?? '' }}">
                </div>

                <div class="filter-bar-group">
                    <label for="period_end">{{ __('End Date', 'fmr') }}</label>
                    <input type="date" name="period_end" id="period_end" value="{{ $_REQUEST['period_end'] ?? '' }}">
                </div>

                <div class="filter-actions">
                    <input type="submit" id="post-query-submit" class="button button-primary" value="{{ __('Apply filters', 'fmr') }}">
                    <a href="{{ admin_url('admin.php?page=assignments') }}" class="button">{{ __('Clear filters', 'fmr') }}</a>
                </div>
            </div>
        </div>

        <input type="hidden" name="page" value="{!! esc_attr($_REQUEST['page']) !!}"/>
    </form>

    <form method="post">
        @php
            wp_nonce_field('bulk-' . $list->_args['plural']);
            $list->display();
        @endphp
    </form>
</div>
