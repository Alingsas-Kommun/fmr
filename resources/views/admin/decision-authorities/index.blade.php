<div class="wrap">
    <h1 class="wp-heading-inline">{{ __('Decision Authorities', 'fmr') }}</h1>
    <a href="{{ admin_url('admin.php?page=decision_authority_edit') }}" class="page-title-action">{{ __('Add New Decision Authority', 'fmr') }}</a>
    <hr class="wp-header-end">

    <form action="" method="GET">
        @php
            $list->prepare_items();
            $list->views();
            $list->search_box(__('Search Decision Authorities', 'fmr'), 'decision_authorities');
        @endphp

        <div class="clear"></div>

        <div class="filter-bar">            
            <div class="filter-row">
                @if(isset($filter_data['boards']) && count($filter_data['boards']) > 0)
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
                @endif

                <div class="filter-bar-group">
                    <label for="start_date">{{ __('Start Date', 'fmr') }}</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $_REQUEST['start_date'] ?? '' }}">
                </div>

                <div class="filter-bar-group">
                    <label for="end_date">{{ __('End Date', 'fmr') }}</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $_REQUEST['end_date'] ?? '' }}">
                </div>

                <div class="filter-actions">
                    <input type="submit" name="filter_action" id="post-query-submit" class="button button-primary" value="{{ __('Apply filters', 'fmr') }}">
                    <a href="{{ admin_url('admin.php?page=decision_authorities') }}" class="button">{{ __('Clear filters', 'fmr') }}</a>
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
