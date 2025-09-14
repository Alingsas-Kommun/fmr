<div x-data="relationHandler({{ json_encode($config) }}, {{ json_encode($existing_data) }})" x-init="init()">
    <input type="hidden" 
        name="{{ $config['storage_key'] }}" 
        id="{{ $config['storage_key'] }}" 
        value="{{ json_encode($existing_data) }}"
    >

    <p>
        <button type="button" @click="showNewForm = !showNewForm" class="button button-secondary" style="display: flex; align-items: center;">
            <span x-show="!showNewForm" class="dashicons dashicons-plus-alt2" style="font-size: 16px; line-height: 1; margin-right: 5px; vertical-align: middle;"></span>
            <span x-show="showNewForm" class="dashicons dashicons-no" style="font-size: 16px; line-height: 1; margin-right: 5px; vertical-align: middle;"></span>
            <span x-text="showNewForm ? '{{ __('Cancel', 'fmr') }}' : '{{ __('Add New', 'fmr') }} ' + '{{ ucfirst($config['entity']) }}'"></span>
        </button>
    </p>

    <div x-show="showNewForm" class="relation-handler-new-form" style="display: none;">
        <table class="form-table">
            <tbody>
                @foreach($config['fields'] as $field)
                    <tr>
                        <th scope="row">
                            <label for="new_{{ $field['key'] }}">{{ $field['label'] }}
                                @if($field['required'])
                                    <span class="description">(required)</span>
                                @endif
                            </label>
                        </th>
                        <td>
                            @if($field['type'] === 'text' || $field['type'] === 'date')
                                <input type="{{ $field['type'] }}" 
                                       id="new_{{ $field['key'] }}"
                                       x-model="newRelation.{{ $field['key'] }}"
                                       class="regular-text">
                            
                            @elseif($field['type'] === 'select')
                                <select id="new_{{ $field['key'] }}"
                                        x-model="newRelation.{{ $field['key'] }}"
                                        class="regular-text">
                                    <option value="">{{ sprintf(__('Select %s', 'fmr'), $field['label']) }}</option>
                                    <template x-for="(label, value) in getFieldOptions('{{ $field['key'] }}')" :key="value">
                                        <option :value="value" :selected="value == newRelation.{{ $field['key'] }}" x-text="label"></option>
                                    </template>
                                </select>
                            
                            @elseif($field['type'] === 'checkbox')
                                <label>
                                    <input type="checkbox" 
                                           id="new_{{ $field['key'] }}"
                                           x-model="newRelation.{{ $field['key'] }}">
                                    <span x-text="newRelation.{{ $field['key'] }} ? 'Yes' : 'No'"></span>
                                </label>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <p class="submit">
            <button type="button" @click="addRelation()" class="button button-primary" style="display: flex; align-items: center;">
                <span class="dashicons dashicons-yes" style="font-size: 16px; line-height: 1; margin-right: 5px; vertical-align: middle;"></span>
                {{ sprintf(__('Add %s', 'fmr'), ucfirst($config['entity'])) }}
            </button>
            
            <button type="button" @click="showNewForm = false; initializeNewRelation()" class="button" style="display: flex; align-items: center;">
                <span class="dashicons dashicons-no" style="font-size: 16px; line-height: 1; margin-right: 5px; vertical-align: middle;"></span>
                {{ __('Cancel', 'fmr') }}
            </button>
        </p>
    </div>

    <div class="relation-handler-lists">
        <div x-show="groupedRelations.ongoing.length > 0">
            <h4>{{ sprintf(__('Ongoing %s', 'fmr'), ucfirst($config['entity_plural'])) }}</h4>
            <div class="relation-list">
                <template x-for="relation in groupedRelations.ongoing" :key="relation.id">
                    @include('admin.relation-handler.relation-row', ['config' => $config])
                </template>
            </div>
        </div>

        <div x-show="groupedRelations.historical.length > 0">
            <h4>{{ sprintf(__('Historical %s', 'fmr'), ucfirst($config['entity_plural'])) }}</h4>
            <div class="relation-list">
                <template x-for="relation in groupedRelations.historical" :key="relation.id">
                    @include('admin.relation-handler.relation-row', ['config' => $config])
                </template>
            </div>
        </div>

        <div x-show="relations.length === 0">
            <p class="description">{!! sprintf(__('No %s found. Add one using the form above.', 'fmr'), $config['entity_plural']) !!}</p>
        </div>
    </div>
</div>
