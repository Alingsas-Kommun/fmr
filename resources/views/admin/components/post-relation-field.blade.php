<select id="{{ $id }}" name="{{ $name }}" class="widefat" {{ !$optional ? 'required' : '' }}>
    <option value="">{{ sprintf(__('Select %s', 'fmr'), $label) }}</option>
    
    @foreach($options as $option_value => $option_label)
        <option value="{{ esc_attr($option_value) }}" 
                {{ $value == $option_value ? 'selected' : '' }}>
            {{ esc_html($option_label) }}
        </option>
    @endforeach
</select>
