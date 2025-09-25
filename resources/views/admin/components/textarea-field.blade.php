<textarea id="{{ $id }}" name="{{ $name }}" class="widefat" rows="{{ $rows }}"
    {{ !$optional ? 'required' : '' }}>{{ esc_textarea($value) }}
</textarea>
