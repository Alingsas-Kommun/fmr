<select id="{{ $id }}" name="{{ $name }}" @class(['widefat' => $fullWidth]) {{ !$optional ? 'required' : '' }}>
    @foreach($options as $option_value => $option_label)
        <option value="{{ esc_attr($option_value) }}" 
                {{ $value == $option_value ? 'selected' : '' }}>
            {{ esc_html($option_label) }}
        </option>
    @endforeach
</select>
