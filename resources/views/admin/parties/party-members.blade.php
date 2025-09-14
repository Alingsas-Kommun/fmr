@php
    $member_sections = [
        [
            'members' => $active_members,
            'title' => __('Active Members', 'fmr'),
            'status_class' => 'active',
            'status_text' => __('Active', 'fmr')
        ],
        [
            'members' => $inactive_members,
            'title' => __('Inactive Members', 'fmr'),
            'status_class' => 'inactive',
            'status_text' => __('Inactive', 'fmr')
        ]
    ];
@endphp

<div class="party-members-meta-box">
    @foreach($member_sections as $section)
        @if($section['members']->count() > 0)
            <div class="party-members-section">
                <h4 class="party-members-section-title">
                    {{ $section['title'] }} 
                    <span class="party-members-count">({{ $section['members']->count() }})</span>
                </h4>

                <div class="party-members-list {{ $section['status_class'] }}-members">
                    @foreach($section['members'] as $member)
                        @include('admin.parties.party-member-item', [
                            'member' => $member,
                            'status_class' => $section['status_class'],
                            'status_text' => $section['status_text']
                        ])
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach

    @if($active_members->count() === 0 && $inactive_members->count() === 0)
        <div class="party-members-empty">
            <p class="description">{{ __('No members found for this party.', 'fmr') }}</p>
        </div>
    @endif
</div>
