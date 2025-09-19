<div class="fields-field-group">
    <div class="fields-field">
        <label for="{{ $field['id'] }}">
            {{ $field['label'] }}
            @if($field['optional'] ?? false)
                <span class="optional-label">({{ __('Optional', 'fmr') }})</span>
            @endif
        </label>

        @switch($field['type'])
            @case('text')
                <input 
                    type="text" 
                    id="{{ $field['id'] }}" 
                    name="{{ $field['id'] }}" 
                    value="{{ esc_attr($value) }}"
                    class="widefat"
                    {{ !($field['optional'] ?? false) ? 'required' : '' }}
                >
                @break

            @case('textarea')
                <textarea 
                    id="{{ $field['id'] }}" 
                    name="{{ $field['id'] }}" 
                    class="widefat" 
                    rows="4"
                    {{ !($field['optional'] ?? false) ? 'required' : '' }}>{{ esc_textarea($value) }}
                </textarea>
                @break

            @case('select')
                <select id="{{ $field['id'] }}" 
                        name="{{ $field['id'] }}" 
                        class="widefat"
                        {{ !($field['optional'] ?? false) ? 'required' : '' }}>
                    @foreach($field['options'] as $option_value => $option_label)
                        <option value="{{ esc_attr($option_value) }}" 
                                {{ $value == $option_value ? 'selected' : '' }}>
                            {{ esc_html($option_label) }}
                        </option>
                    @endforeach
                </select>
                @break

            @case('checkbox')
                <x-admin.toggle-switch
                    :id="$field['id']"
                    :name="$field['id']"
                    :checked="$value"
                    :on-label="$field['on_label'] ?? __('On', 'fmr')"
                    :off-label="$field['off_label'] ?? __('Off', 'fmr')"
                />
                @break

            @case('radio')
                <div class="radio-group">
                    @foreach($field['options'] as $option_value => $option_label)
                        <label class="radio-label">
                            <input type="radio" 
                                name="{{ $field['id'] }}" 
                                value="{{ esc_attr($option_value) }}"
                                {{ $value == $option_value ? 'checked' : '' }}
                                {{ !($field['optional'] ?? false) ? 'required' : '' }}>
                            <span class="radio-text">{{ esc_html($option_label) }}</span>
                        </label>
                    @endforeach
                </div>
            @break

            @case('url')
                <input type="url" 
                    id="{{ $field['id'] }}" 
                    name="{{ $field['id'] }}" 
                    value="{{ esc_attr($value) }}"
                    class="widefat"
                    {{ !($field['optional'] ?? false) ? 'required' : '' }}>
                @break

            @case('number')
                <input 
                    type="number" 
                    id="{{ $field['id'] }}" 
                    name="{{ $field['id'] }}" 
                    value="{{ esc_attr($value) }}"
                    min="{{ $field['min'] ?? '' }}"
                    max="{{ $field['max'] ?? '' }}"
                    step="{{ $field['step'] ?? '1' }}"
                    class="widefat"
                    {{ !($field['optional'] ?? false) ? 'required' : '' }}
                >
                @break

            @case('date')
                <input 
                    type="date" 
                    id="{{ $field['id'] }}" 
                    name="{{ $field['id'] }}" 
                    value="{{ esc_attr($value) }}"
                    class="widefat"
                    {{ !($field['optional'] ?? false) ? 'required' : '' }}
                >
                @break

            @case('email')
                <input 
                    type="email" 
                    id="{{ $field['id'] }}" 
                    name="{{ $field['id'] }}" 
                    value="{{ esc_attr($value) }}"
                    class="widefat"
                    {{ !($field['optional'] ?? false) ? 'required' : '' }}
                >
                @break

            @case('tel')
                <input type="tel" 
                    id="{{ $field['id'] }}" 
                    name="{{ $field['id'] }}" 
                    value="{{ esc_attr($value) }}"
                    class="widefat"
                    {{ !($field['optional'] ?? false) ? 'required' : '' }}
                >
                @break

            @case('post_relation')
                <select id="{{ $field['id'] }}" name="{{ $field['id'] }}" class="widefat" {{ !($field['optional'] ?? false) ? 'required' : '' }}>
                    <option value="">{{ sprintf(__('Select %s', 'fmr'), $field['label']) }}</option>
                    
                    @foreach($field['options'] as $option_value => $option_label)
                        <option value="{{ esc_attr($option_value) }}" 
                                {{ $value == $option_value ? 'selected' : '' }}>
                            {{ esc_html($option_label) }}
                        </option>
                    @endforeach
                </select>
                @break
        @endswitch

        @if(!empty($field['description']))
            <p class="description">{{ esc_html($field['description']) }}</p>
        @endif
    </div>

    @if(isset($field['visibility']))
        <x-admin.visibility-toggle
            class="fields-field-visibility"
            :name="$field['visibility']['id']"
            :is-visible="$field['visibility']['value'] ?? $field['visibility']['default'] ?? true" 
        />
    @endif
</div>