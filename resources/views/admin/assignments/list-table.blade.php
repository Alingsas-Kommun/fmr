<div class="wrap">
    <h1 class="wp-heading-inline">{{ __('Assignments', 'fmr') }}</h1>
    <a href="{{ admin_url('admin.php?page=assignment_edit') }}" class="page-title-action">{{ __('Add new assignment', 'fmr') }}</a>
    <hr class="wp-header-end">
    
    <form method="post">
        @php
            wp_nonce_field('bulk-' . $list_table->_args['plural']);
            $list_table->prepare_items();
            $list_table->views();
            $list_table->search_box(__('Search Assignments', 'fmr'), 'assignment');
            $list_table->display();
        @endphp
    </form>
</div>
