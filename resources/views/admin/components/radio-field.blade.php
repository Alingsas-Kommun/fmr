<div class="radio-group">
    @foreach($options as $option_value => $option_label)
        <label class="radio-label">
            <input type="radio" name="{{ $name }}" value="{{ esc_attr($option_value) }}" {{ $value == $option_value ? 'checked' : '' }} {{ !$optional ? 'required' : '' }}>
            <span class="radio-text">{{ esc_html($option_label) }}</span>
        </label>
    @endforeach
</div>
