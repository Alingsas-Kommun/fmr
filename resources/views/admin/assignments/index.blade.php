<div class="wrap">
    <h1 class="wp-heading-inline">{{ __('Assignments', 'fmr') }}</h1>
    <a href="{{ admin_url('admin.php?page=assignment_edit') }}" class="page-title-action">{{ __('Add new assignment', 'fmr') }}</a>
    <hr class="wp-header-end">
    
    <form method="post">
        @php
            wp_nonce_field('bulk-' . $list->_args['plural']);
            $list->prepare_items();
            $list->views();
            $list->search_box(__('Search Assignments', 'fmr'), 'assignment');
            $list->display();
        @endphp
    </form>
</div>
