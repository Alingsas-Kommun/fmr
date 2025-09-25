<div class="key-generation-field">
    <div class="key-display" style="{{ $value ? '' : 'display: none;' }}">
        <div class="key-value">
            <code id="{{ $id }}_display">{{ $value ? str_repeat('*', strlen($value)) : '' }}</code>
            
            <button type="button" class="button button-small" id="{{ $id }}_toggle">
                {{ __('Show', 'fmr') }}
            </button>
        </div>
        <div class="key-actions">
            <button type="button" class="button button-secondary" id="{{ $id }}_copy">
                {{ __('Copy Key', 'fmr') }}
            </button>

            <button type="button" class="button button-secondary" id="{{ $id }}_generate">
                {{ __('Generate New Key', 'fmr') }}
            </button>
        </div>
    </div>
    
    <input type="hidden" id="{{ $id }}" name="{{ $name }}" value="{{ esc_attr($value) }}">
    
    @if(!$value)
        <button type="button" class="button button-primary" id="{{ $id }}_generate_first">
            {{ __('Generate Key', 'fmr') }}
        </button>
    @endif
</div>