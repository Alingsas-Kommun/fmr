<div {{ $attributes->merge($attr) }}>
    <input type="checkbox" 
           id="{{ $id }}" 
           name="{{ $name }}" 
           value="1"
           class="toggle-switch-checkbox"
           {{ $checked ? 'checked' : '' }}>
    
    <label class="toggle-switch-label" for="{{ $id }}">
        <div class="toggle-switch-track">
            <span class="toggle-switch-text toggle-switch-text-on">{{ $onLabel }}</span>
            <span class="toggle-switch-text toggle-switch-text-off">{{ $offLabel }}</span>
        </div>
        <span class="toggle-switch-handle"></span>
    </label>
</div>