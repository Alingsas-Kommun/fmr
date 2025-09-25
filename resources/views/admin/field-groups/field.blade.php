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
                <x-admin.input-field
                    :id="$field['id']"
                    :name="$field['id']"
                    :value="$value"
                    :optional="$field['optional'] ?? false"
                    :label="$field['label']"
                    :description="$field['description'] ?? ''"
                    type="text"
                />
                @break

            @case('textarea')
                <x-admin.textarea-field
                    :id="$field['id']"
                    :name="$field['id']"
                    :value="$value"
                    :optional="$field['optional'] ?? false"
                    :label="$field['label']"
                    :description="$field['description'] ?? ''"
                    :rows="$field['rows'] ?? 4"
                />
                @break

            @case('select')
                <x-admin.select-field
                    :id="$field['id']"
                    :name="$field['id']"
                    :value="$value"
                    :optional="$field['optional'] ?? false"
                    :label="$field['label']"
                    :description="$field['description'] ?? ''"
                    :options="$field['options'] ?? []"
                />
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
                <x-admin.radio-field
                    :id="$field['id']"
                    :name="$field['id']"
                    :value="$value"
                    :optional="$field['optional'] ?? false"
                    :label="$field['label']"
                    :description="$field['description'] ?? ''"
                    :options="$field['options'] ?? []"
                />
                @break

            @case('url')
                <x-admin.input-field
                    :id="$field['id']"
                    :name="$field['id']"
                    :value="$value"
                    :optional="$field['optional'] ?? false"
                    :label="$field['label']"
                    :description="$field['description'] ?? ''"
                    type="url"
                />
                @break

            @case('number')
                <x-admin.input-field
                    :id="$field['id']"
                    :name="$field['id']"
                    :value="$value"
                    :optional="$field['optional'] ?? false"
                    :label="$field['label']"
                    :description="$field['description'] ?? ''"
                    type="number"
                    :min="$field['min'] ?? null"
                    :max="$field['max'] ?? null"
                    :step="$field['step'] ?? '1'"
                />
                @break

            @case('date')
                <x-admin.input-field
                    :id="$field['id']"
                    :name="$field['id']"
                    :value="$value"
                    :optional="$field['optional'] ?? false"
                    :label="$field['label']"
                    :description="$field['description'] ?? ''"
                    type="date"
                />
                @break

            @case('email')
                <x-admin.input-field
                    :id="$field['id']"
                    :name="$field['id']"
                    :value="$value"
                    :optional="$field['optional'] ?? false"
                    :label="$field['label']"
                    :description="$field['description'] ?? ''"
                    type="email"
                />
                @break

            @case('tel')
                <x-admin.input-field
                    :id="$field['id']"
                    :name="$field['id']"
                    :value="$value"
                    :optional="$field['optional'] ?? false"
                    :label="$field['label']"
                    :description="$field['description'] ?? ''"
                    type="tel"
                />
                @break

            @case('post_relation')
                <x-admin.post-relation-field
                    :id="$field['id']"
                    :name="$field['id']"
                    :value="$value"
                    :optional="$field['optional'] ?? false"
                    :label="$field['label']"
                    :description="$field['description'] ?? ''"
                    :options="$field['options'] ?? []"
                />
                @break

            @case('image')
                <x-admin.image-field
                    :id="$field['id']"
                    :name="$field['id']"
                    :value="$value"
                    :optional="$field['optional'] ?? false"
                    :label="$field['label']"
                    :description="$field['description'] ?? ''"
                />
                @break

            @case('color')
                <x-admin.color-field
                    :id="$field['id']"
                    :name="$field['id']"
                    :value="$value"
                    :default="$field['default'] ?? '#000000'"
                    :optional="$field['optional'] ?? false"
                    :label="$field['label']"
                    :description="$field['description'] ?? ''"
                    :css-var="$field['css_var'] ?? ''"
                />
                @break

            @case('key_generation')
                <x-admin.key-generation-field
                    :id="$field['id']"
                    :name="$field['id']"
                    :value="$value"
                    :optional="$field['optional'] ?? false"
                    :label="$field['label']"
                    :description="$field['description'] ?? ''"
                />
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