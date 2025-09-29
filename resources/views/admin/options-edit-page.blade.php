<div class="wrap">
    <h1 class="wp-heading-inline">{{ $pageTitle }}</h1>

    <hr class="wp-header-end">

    @if($errorMessage)
        <div class="notice notice-error">
            <p>{{ $errorMessage }}</p>
        </div>
    @endif

    <form method="post" action="{{ admin_url('admin-post.php') }}">
        @php
            wp_nonce_field($nonceAction, $nonceField);
        @endphp

        <input type="hidden" name="action" value="{{ $formAction }}">
        <input type="hidden" name="_wp_http_referer" value="{{ admin_url('admin.php?page=' . $pageSlug) }}">
        <input type="hidden" id="user-id" name="user_ID" value="{{ get_current_user_id() }}">
        <input type="hidden" id="meta-box-order-nonce" name="meta-box-order-nonce" value="{{ wp_create_nonce('meta-box-order') }}">
        <input type="hidden" id="closedpostboxesnonce" name="closedpostboxesnonce" value="{{ wp_create_nonce('closedpostboxes') }}">

        <div id="poststuff">
            <div id="post-body" class="metabox-holder {!! get_user_meta(get_current_user_id(), "screen_layout_{$hook}", true) == '1' ? 'columns-1' : 'columns-2' !!}">
                <div id="post-body-content">
                    @php
                        do_meta_boxes($hook, 'normal', null);
                        do_meta_boxes($hook, 'advanced', null);
                    @endphp
                </div>

                <div id="postbox-container-1" class="postbox-container">
                    @php
                        do_meta_boxes($hook, 'side', null);
                    @endphp
                </div>
            </div>

            <br class="clear" />
        </div>
    </form>
</div>

<script>
    jQuery(document).ready(function($){
        postboxes.add_postbox_toggles('{{ $hook }}');
    });
</script>
