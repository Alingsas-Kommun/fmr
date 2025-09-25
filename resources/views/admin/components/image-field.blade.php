<div class="image-field-wrapper">
    <div class="image-preview" id="{{ $id }}_preview" style="{{ $value ? '' : 'display: none;' }}">
        @if($value)
            @php
                $image = wp_get_attachment_image($value, 'medium', false, ['class' => 'max-w-full h-auto']);
            @endphp
            {!! $image !!}
        @endif
        
        <div class="image-overlay-actions">
            <button type="button" class="button button-small" id="{{ $id }}_select" title="{{ $value ? __('Change Image', 'fmr') : __('Select Image', 'fmr') }}">
                <span class="dashicons dashicons-edit"></span>
            </button>

            @if($value)
                <button type="button" class="button button-small" id="{{ $id }}_remove" title="{{ __('Remove Image', 'fmr') }}">
                    <span class="dashicons dashicons-trash"></span>
                </button>
            @endif
        </div>
    </div>
    
    <input type="hidden" id="{{ $id }}" name="{{ $name }}" value="{{ esc_attr($value) }}">
    
    @if(!$value)
        <div class="image-actions">
            <button type="button" class="button button-secondary" id="{{ $id }}_select">
                {{ __('Select Image', 'fmr') }}
            </button>
        </div>
    @endif
</div>