<div class="relation-row" style="border: 1px solid #ddd; margin-bottom: 10px; background: #fff;">
    <div class="relation-header" style="padding: 10px; border-bottom: 1px solid #eee; cursor: pointer; background: #f9f9f9;"
         @click="toggleRelation(relation.id)">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div style="flex: 1;">
                <strong x-text="getRelationTitle(relation)"></strong>
                <span x-show="getRelationSubtitle(relation)" 
                      style="color: #666; margin-left: 10px;" 
                      x-text="getRelationSubtitle(relation)"></span>
                <span x-show="getRelationFieldValue(relation)" 
                      style="color: #999; margin-left: 10px; font-size: 12px;" 
                      x-text="getRelationFieldValue(relation)"></span>
                <span x-show="relation.hasChanges" 
                      style="color: #d63638; margin-left: 10px; font-size: 12px;">
                    {!! __('(unsaved changes)', 'fmr') !!}
                </span>
                <span x-show="relation.isNew" style="color: #00a32a; margin-left: 10px; font-size: 12px;">
                    {!! __('(new)', 'fmr') !!}
                </span>
            </div>
            <div style="display: flex; align-items: center;">
                <button type="button" 
                        @click.stop="deleteRelation(relation.id)"
                        class="button button-link-delete"
                        style="margin-right: 10px; background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 4px 8px; border-radius: 3px; display: flex; align-items: center;">
                    <span class="dashicons dashicons-trash" style="font-size: 16px; line-height: 1; vertical-align: middle;"></span>
                </button>
                <span x-show="isExpanded(relation.id)" class="dashicons dashicons-arrow-up-alt2" style="font-size: 16px; color: #666; cursor: pointer; vertical-align: middle;"></span>
                <span x-show="!isExpanded(relation.id)" class="dashicons dashicons-arrow-down-alt2" style="font-size: 16px; color: #666; cursor: pointer; vertical-align: middle;"></span>
            </div>
        </div>
    </div>

    <!-- Relation Content -->
    <div x-show="isExpanded(relation.id)" style="display: none; padding: 15px;">
        <table class="form-table">
            <tbody>
                @foreach($config['fields'] as $field)
                    <tr>
                        <th scope="row">
                            <label :for="'relation_{{ $field['key'] }}_' + (relation.id || 'new')">{{ $field['label'] }}
                                @if($field['required'])
                                    <span class="description">({{ __('required', 'fmr') }})</span>
                                @endif
                            </label>
                        </th>
                        <td>
                            @if($field['type'] === 'text' || $field['type'] === 'date')
                                <input 
                                    type="{{ $field['type'] }}" 
                                    :id="'relation_{{ $field['key'] }}_' + (relation.id || 'new')"
                                    :value="{{ $field['type'] === 'date' ? 'formatDateForInput(relation.' . $field['key'] . ')' : 'relation.' . $field['key'] }}"
                                    @input="updateRelation(relation.id, '{{ $field['key'] }}', $event.target.value)"
                                    class="regular-text"
                                >
                            
                            @elseif($field['type'] === 'select')
                                <select 
                                    :id="'relation_{{ $field['key'] }}_' + (relation.id || 'new')"
                                    :value="relation.{{ $field['key'] }}"
                                    @change="updateRelation(relation.id, '{{ $field['key'] }}', $event.target.value)"
                                    class="regular-text"
                                >
                                    <option value="">{{ sprintf(__('Select %s', 'fmr'), $field['label']) }}</option>
                                    
                                    <template x-for="(label, value) in getFieldOptions('{{ $field['key'] }}')" :key="value">
                                        <option :value="value" :selected="value == relation.{{ $field['key'] }}" x-text="label"></option>
                                    </template>
                                </select>
                            
                            @elseif($field['type'] === 'checkbox')
                                <label>
                                    <input type="checkbox" 
                                        :id="'relation_{{ $field['key'] }}_' + (relation.id || 'new')"
                                        :checked="relation.{{ $field['key'] }}"
                                        @change="updateRelation(relation.id, '{{ $field['key'] }}', $event.target.checked)"
                                    >
                                    <span x-text="relation.{{ $field['key'] }} ? '{{ __('Yes', 'fmr') }}' : '{{ __('No', 'fmr') }}'"></span>
                                </label>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
