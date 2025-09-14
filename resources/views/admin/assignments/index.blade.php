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

        <input type="hidden" name="page" value="{!! esc_attr($_REQUEST['page']) !!}"/>
    </form>

    <form method="post">
        @php
            wp_nonce_field('bulk-' . $list->_args['plural']);
            $list->display();
        @endphp
    </form>
</div>
