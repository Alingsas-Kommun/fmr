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

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
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
