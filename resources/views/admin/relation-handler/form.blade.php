@props(['fields', 'prefix' => 'new', 'model' => 'newRelation', 'relationId' => null])

<div class="relation-fields-grid">
    @foreach($fields as $field)
        <div class="relation-field relation-field-cols-{{ $field['cols'] ?? 12 }}" data-field-key="{{ $field['key'] }}">
            <label 
                class="relation-field-label"

                @if($prefix === 'new')
                    for="{{ $prefix }}_{{ $field['key'] }}"
                @else
                    :for="'{{ $prefix }}_{{ $field['key'] }}_' + ({{ $relationId }} || 'new')"
                @endif
            >
                {{ $field['label'] }}
                
                @if($field['optional'])
                    <span class="description">{!! __('Optional', 'fmr') !!}</span>
                @endif
            </label>
            
            <div class="relation-field-input">
                @if($field['type'] === 'text' || $field['type'] === 'date')
                    <input 
                        class="regular-text"
                        type="{{ $field['type'] }}" 

                        @if($prefix === 'new')
                            id="{{ $prefix }}_{{ $field['key'] }}"
                            x-model="{{ $model }}.{{ $field['key'] }}"
                            @input="clearFieldValidation('{{ $field['key'] }}')"
                        @else
                            :id="'{{ $prefix }}_{{ $field['key'] }}_' + ({{ $relationId }} || 'new')"
                            :value="{{ $field['type'] === 'date' ? 'formatDateForInput(' . $model . '.' . $field['key'] . ')' : $model . '.' . $field['key'] }}"
                            @input="updateRelation({{ $relationId }}, '{{ $field['key'] }}', $event.target.value); clearFieldValidation('{{ $field['key'] }}')"
                        @endif

                        @if(!$field['optional'])
                            @if($prefix === 'new')
                                :required="showNewForm"
                            @else
                                required
                            @endif
                        @endif
                    >
                
                @elseif($field['type'] === 'select')
                    <select 
                        class="regular-text"

                        @if($prefix === 'new')
                            id="{{ $prefix }}_{{ $field['key'] }}"
                            x-model="{{ $model }}.{{ $field['key'] }}"
                            @change="clearFieldValidation('{{ $field['key'] }}')"
                        @else
                            :id="'{{ $prefix }}_{{ $field['key'] }}_' + ({{ $relationId }} || 'new')"
                            :value="{{ $model }}.{{ $field['key'] }}"
                            @change="updateRelation({{ $relationId }}, '{{ $field['key'] }}', $event.target.value); clearFieldValidation('{{ $field['key'] }}')"
                        @endif

                        @if(!$field['optional'])
                            @if($prefix === 'new')
                                :required="showNewForm"
                            @else
                                required
                            @endif
                        @endif
                    >
                        <option value="">{{ sprintf(__('Select %s', 'fmr'), $field['label']) }}</option>
                        
                        <template x-for="(label, value) in getFieldOptions('{{ $field['key'] }}')" :key="value">
                            <option :value="value" :selected="value == {{ $model }}.{{ $field['key'] }}" x-text="label"></option>
                        </template>
                    </select>
                
                @elseif($field['type'] === 'select-grouped')
                    <select 
                        class="regular-text"

                        @if($prefix === 'new')
                            id="{{ $prefix }}_{{ $field['key'] }}"
                            x-model="{{ $model }}.{{ $field['key'] }}"
                            @change="clearFieldValidation('{{ $field['key'] }}')"
                        @else
                            :id="'{{ $prefix }}_{{ $field['key'] }}_' + ({{ $relationId }} || 'new')"
                            :value="{{ $model }}.{{ $field['key'] }}"
                            @change="updateRelation({{ $relationId }}, '{{ $field['key'] }}', $event.target.value); clearFieldValidation('{{ $field['key'] }}')"
                        @endif
                        
                        @if(!$field['optional'])
                            @if($prefix === 'new')
                                :required="showNewForm"
                            @else
                                required
                            @endif
                        @endif
                    >
                        <option value="">{{ sprintf(__('Select %s', 'fmr'), $field['label']) }}</option>
                        
                        <template x-for="(groupOptions, groupLabel) in getFieldOptions('{{ $field['key'] }}')" :key="groupLabel">
                            <optgroup :label="groupLabel">
                                <template x-for="(label, value) in groupOptions" :key="value">
                                    <option :value="value" :selected="value == {{ $model }}.{{ $field['key'] }}" x-text="label"></option>
                                </template>
                            </optgroup>
                        </template>
                    </select>
                
                @elseif($field['type'] === 'checkbox')
                    <label class="checkbox-label">
                        <input 
                            type="checkbox"

                            @if($prefix === 'new')
                                id="{{ $prefix }}_{{ $field['key'] }}"
                                x-model="{{ $model }}.{{ $field['key'] }}"
                                @change="clearFieldValidation('{{ $field['key'] }}')"
                            @else
                                :id="'{{ $prefix }}_{{ $field['key'] }}_' + ({{ $relationId }} || 'new')"
                                :checked="{{ $model }}.{{ $field['key'] }}"
                                @change="updateRelation({{ $relationId }}, '{{ $field['key'] }}', $event.target.checked); clearFieldValidation('{{ $field['key'] }}')"
                            @endif

                            @if(!$field['optional'])
                                @if($prefix === 'new')
                                    :required="showNewForm"
                                @else
                                    required
                                @endif
                            @endif
                        >
                        <span x-text="{{ $model }}.{{ $field['key'] }} ? '{{ __('Yes', 'fmr') }}' : '{{ __('No', 'fmr') }}'"></span>
                    </label>
                @endif
            </div>
        </div>
    @endforeach
</div>
