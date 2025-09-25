<div class="color-field-wrapper">
    <input 
        type="color" 
        id="{{ $id }}" 
        name="{{ $name }}" 
        value="{{ esc_attr($value) }}"
        class="color-picker"
        data-default="{{ esc_attr($default) }}"
        data-css-var="{{ $cssVar ?? '' }}"
        {{ !$optional ? 'required' : '' }}
    >
    <input 
        type="text" 
        id="{{ $id }}_text" 
        value="{{ esc_attr($value) }}"
        class="color-text-input"
        placeholder="#000000"
        pattern="^#[0-9A-Fa-f]{6}$"
        maxlength="7"
    >
    <button 
        type="button" 
        class="button button-small color-reset" 
        id="{{ $id }}_reset"
        title="{{ __('Reset to default', 'fmr') }}"
    >
        <span class="dashicons dashicons-undo"></span>
    </button>
</div>