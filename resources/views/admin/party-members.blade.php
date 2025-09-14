<div class="party-members-meta-box">
    @if($active_members->count() > 0)
        <h4>{{ __('Active Members', 'fmr') }} ({{ $active_members->count() }})</h4>
        <ul class="party-members-list active-members">
            @foreach($active_members as $member)
                <li>
                    <a href="{{ get_edit_post_link($member->ID) }}">
                        {{ $member->post_title }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

    @if($inactive_members->count() > 0)
        <h4>{{ __('Inactive Members', 'fmr') }} ({{ $inactive_members->count() }})</h4>
        <ul class="party-members-list inactive-members">
            @foreach($inactive_members as $member)
                <li>
                    <a href="{{ get_edit_post_link($member->ID) }}">
                        {{ $member->post_title }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

    @if($active_members->count() === 0 && $inactive_members->count() === 0)
        <p class="description">{{ __('No members found for this party.', 'fmr') }}</p>
    @endif
</div>
