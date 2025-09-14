<div x-data="relationHandler({{ json_encode($config) }}, {{ json_encode($existing_data) }})" x-init="init()">
    <input type="hidden" name="{{ $config['storage_key'] }}" id="{{ $config['storage_key'] }}" value="{{ json_encode($existing_data) }}">

    <p>
        <button type="button" @click="showNewForm = !showNewForm" class="button button-secondary">
            <span x-text="showNewForm ? '{{ __('Cancel', 'fmr') }}' : '{{ __('Add New', 'fmr') }} ' + '{{ $config['entity'] }}'"></span>
        </button>
    </p>

    <div x-show="showNewForm" class="relation-handler-new-form" style="display: none;">
        @include('admin.relation-handler.form', ['fields' => $config['fields'], 'prefix' => 'new', 'model' => 'newRelation'])
        
        <button type="button" @click="addRelation()" class="button button-primary" style="margin-top: 1rem;">
            {{ sprintf(__('Add %s', 'fmr'), $config['entity']) }}
        </button>
    </div>

    <div class="relation-handler-lists">
        <div x-show="groupedRelations.ongoing.length > 0">
            <h4>{{ sprintf(__('Ongoing %s', 'fmr'), $config['entity_plural']) }}</h4>
            
            <div class="relation-list">
                <template x-for="relation in groupedRelations.ongoing" :key="relation.id">
                    @include('admin.relation-handler.row', ['config' => $config])
                </template>
            </div>
        </div>

        <div x-show="groupedRelations.historical.length > 0">
            <h4>{{ sprintf(__('Historical %s', 'fmr'), $config['entity_plural']) }}</h4>
            
            <div class="relation-list">
                <template x-for="relation in groupedRelations.historical" :key="relation.id">
                    @include('admin.relation-handler.row', ['config' => $config])
                </template>
            </div>
        </div>

        <div x-show="relations.length === 0">
            <p class="description">{!! sprintf(__('No %s found. Add one using the form above.', 'fmr'), $config['entity_plural']) !!}</p>
        </div>
    </div>
</div>
