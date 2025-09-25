<input 
    type="{{ $type }}" 
    id="{{ $id }}" 
    name="{{ $name }}" 
    value="{{ esc_attr($value) }}"
    class="widefat"
    @if($min) min="{{ $min }}" @endif
    @if($max) max="{{ $max }}" @endif
    @if($step) step="{{ $step }}" @endif
    {{ !$optional ? 'required' : '' }}
>
