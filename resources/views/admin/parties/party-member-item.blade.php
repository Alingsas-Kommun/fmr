<div class="party-member-item">
    <div class="party-member-content">
        <div class="party-member-thumbnail">
            @set($thumbnail, $member->thumbnail('party-member-avatar'))

            @if($thumbnail)
                {!! $thumbnail !!}
            @else
                <div class="party-member-avatar-placeholder">
                    <span class="party-member-initials">{{ substr($member->post_title, 0, 1) }}</span>
                </div>
            @endif
        </div>
        
        <div class="party-member-info">
            <span class="party-member-name">{{ $member->post_title }}</span>
            <span class="party-member-status {{ $status_class }}">{{ $status_text }}</span>
        </div>
    </div>

    <div class="party-member-actions">
        <a href="{!! get_edit_post_link($member->ID) !!}" class="button button-small" style="padding: 0 6px;">
            <span class="dashicons dashicons-edit" style="margin: 3px 0;"></span>
        </a>
    </div>
</div>
