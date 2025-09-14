<div class="wrap">
    <h1 class="wp-heading-inline">{{ __('Decision Authorities', 'fmr') }}</h1>
    <a href="{{ admin_url('admin.php?page=decision_authority_edit') }}" class="page-title-action">{{ __('Add New Decision Authority', 'fmr') }}</a>
    <hr class="wp-header-end">

    <form method="post">
        @php
            wp_nonce_field('bulk-' . $list->_args['plural']);
            $list->prepare_items();
            $list->views();
            $list->search_box(__('Search Decision Authorities', 'fmr'), 'decision_authorities');
            $list->display();
        @endphp
    </form>
</div>
