<div class="wrap">
    <h1 class="wp-heading-inline">{{ $pageTitle }}</h1>
    <a href="{{ admin_url('admin.php?page=' . $pageSlug) }}" class="page-title-action">{!! $addNewButtonTitle !!}</a>

    <hr class="wp-header-end">

    @if($errorMessage)
        <div class="notice notice-error">
            <p>{{ $errorMessage }}</p>
        </div>
    @endif

    @if($successMessage)
        <div class="notice notice-success">
            <p>{{ $successMessage }}</p>
        </div>
    @endif

    <form method="post" action="{{ admin_url('admin-post.php') }}">
        @php
            wp_nonce_field($nonceAction, $nonceField);
        @endphp

        <input type="hidden" name="action" value="{{ $formAction }}">

        @if($id)
            <input type="hidden" name="id" value="{{ $id }}">
        @endif

        <div id="poststuff">
            <div id="post-body-content">
                <div id="titlediv">
                    @if($showTitleField)
                        <div id="titlewrap">
                            <label class="screen-reader-text" id="title-prompt-text" for="title">Lägg till rubrik</label>
                            <input type="text" name="title" id="title" value="{{ $getFieldValue('title', '') }}" class="widefat" placeholder="{{ __('Enter title...', 'fmr') }}" spellcheck="true" autocomplete="off" required>
                        </div>
                    @endif

                    @if($id && $routeName)
                        @php
                            $permalink = str_replace('/wp-admin/admin.php', '', route($routeName, $id));
                        @endphp
                        
                        <div class="inside">
                            <div id="edit-slug-box">
                                <strong>{!! __('Permalink:', 'fmr') !!}</strong>

                                <span id="sample-permalink">
                                    <a href="{!! $permalink !!}" class="text-primary-500 hover:text-primary-600">
                                        {!! $permalink !!}/
                                    </a>
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
                    @php
                        do_meta_boxes($hook, 'normal', $currentObject);
                        do_meta_boxes($hook, 'advanced', $currentObject);
                    @endphp
                </div>

                <div id="postbox-container-1" class="postbox-container">
                    @php
                        do_meta_boxes($hook, 'side', $currentObject);
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
